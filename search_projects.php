<?php
header('Content-Type: application/json');
require 'datacon.php';

$query      = trim($_GET['query'] ?? '');
$department = trim($_GET['department'] ?? '');
$year       = trim($_GET['year'] ?? '');
$tag        = trim($_GET['tag'] ?? '');

/**
 * Simple English stemmer - strips common suffixes to find word roots.
 * This enables conflation: "computing" matches "computer", "computational", etc.
 */
function stem(string $word): string {
    $word = strtolower(trim($word));
    if (strlen($word) <= 3) return $word;

    // Ordered from longest to shortest to match greedily
    $suffixes = [
        'ational' => 'ate',
        'tional'  => 'tion',
        'encies'  => 'ence',
        'nesses'  => 'ness',
        'ments'   => 'ment',
        'ation'   => 'ate',
        'ising'   => 'ise',
        'izing'   => 'ize',
        'ously'   => 'ous',
        'ively'   => 'ive',
        'ling'    => 'l',
        'ally'    => 'al',
        'ment'    => '',
        'ness'    => '',
        'able'    => '',
        'ible'    => '',
        'tion'    => '',
        'sion'    => '',
        'ence'    => '',
        'ance'    => '',
        'ized'    => 'ize',
        'ised'    => 'ise',
        'ting'    => 't',
        'ning'    => 'n',
        'ring'    => 'r',
        'sing'    => 's',
        'ying'    => 'y',
        'ful'     => '',
        'ous'     => '',
        'ive'     => '',
        'ize'     => '',
        'ise'     => '',
        'ing'     => '',
        'ies'     => 'y',
        'ity'     => '',
        'ist'     => '',
        'ism'     => '',
        'ate'     => '',
        'ent'     => '',
        'ant'     => '',
        'ory'     => '',
        'ary'     => '',
        'ery'     => '',
        'ual'     => '',
        'ial'     => '',
        'ess'     => '',
        'ors'     => '',
        'ers'     => '',
        'ion'     => '',
        'ed'      => '',
        'er'      => '',
        'ly'      => '',
        'es'      => '',
        'al'      => '',
        's'       => '',
    ];

    foreach ($suffixes as $suffix => $replacement) {
        $suffixLen = strlen($suffix);
        if (strlen($word) > $suffixLen + 2 && substr($word, -$suffixLen) === $suffix) {
            return substr($word, 0, -$suffixLen) . $replacement;
        }
    }

    return $word;
}

// Build WHERE conditions
$where  = [];
$params = [];
$types  = '';

// Text search with stemming
if ($query !== '') {
    $keywords = preg_split('/\s+/', $query);
    $stopWords = ['the','a','an','and','or','but','in','on','at','to','for','of','with','by','from','as','is','was','are','were','be','been','it','its','not','no','this','that'];

    foreach ($keywords as $keyword) {
        if (strlen($keyword) < 2 || in_array(strtolower($keyword), $stopWords)) continue;

        $stemmed = stem($keyword);
        $like = "%{$keyword}%";
        $stemLike = "%{$stemmed}%";

        // Search both original and stemmed terms across title, synopsis, tags, students, dept, supervisor
        $clause = "(p.title LIKE ? OR p.title LIKE ? OR p.synopsis LIKE ? OR p.synopsis LIKE ? OR pm.student_name LIKE ? OR t.name LIKE ? OR t.name LIKE ? OR d.dep_name LIKE ? OR CONCAT(s.first_name,' ',s.last_name) LIKE ?)";
        $where[] = $clause;
        array_push($params, $like, $stemLike, $like, $stemLike, $like, $like, $stemLike, $like, $like);
        $types .= 'sssssssss';
    }
}

// Department filter
if ($department !== '') {
    $where[]  = "p.dep_id = ?";
    $params[] = $department;
    $types   .= 's';
}

// Year filter
if ($year !== '') {
    $where[]  = "p.year = ?";
    $params[] = (int)$year;
    $types   .= 'i';
}

// Tag filter (exact match by tag name)
if ($tag !== '') {
    $where[]  = "t.name = ?";
    $params[] = $tag;
    $types   .= 's';
}

// If no filters at all, return empty
if (empty($where)) {
    echo json_encode([]);
    exit;
}

$whereSql = implode(' AND ', $where);

$sql = "
SELECT
  p.id,
  p.title,
  p.synopsis,
  p.year,
  d.dep_name,
  p.file_path,
  GROUP_CONCAT(DISTINCT pm.student_name SEPARATOR ', ') AS student_names,
  GROUP_CONCAT(DISTINCT t.name SEPARATOR ', ') AS tags,
  GROUP_CONCAT(DISTINCT CONCAT(s.first_name,' ',s.last_name) SEPARATOR ', ') AS supervisors
FROM projects p
JOIN departments d ON p.dep_id = d.dep_id
LEFT JOIN project_members pm ON p.id = pm.project_id
LEFT JOIN project_tags pt ON p.id = pt.project_id
LEFT JOIN tags t ON pt.tag_id = t.id
LEFT JOIN project_supervisors ps ON p.id = ps.project_id
LEFT JOIN supervisors s ON ps.supervisor_id = s.id
WHERE {$whereSql}
GROUP BY p.id
ORDER BY p.year DESC
LIMIT 50
";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$projects = [];
while ($row = $result->fetch_assoc()) {
    $projects[] = $row;
}

echo json_encode($projects);
