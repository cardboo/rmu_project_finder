<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login/");
    exit();
}

require "../datacon.php";
require "../csrf.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf();

    $project_id = (int)$_POST['project_id'];
    $title = trim($_POST['project_title']);
    $description = trim($_POST['description']);
    $year = (int)$_POST['year'];
    $supervisors = $_POST['supervisors'] ?? [];
    $student_names = $_POST['student_names'] ?? [];
    $index_numbers = $_POST['index_numbers'] ?? [];
    $tags = isset($_POST['tags']) ? array_map('trim', explode(',', $_POST['tags'])) : [];

    // FIX: existing file path from modal
    $existing_file = $_POST['existing_file_path'] ?? '';

    if ($project_id <= 0) die("Invalid Project ID.");
    if (!$title || !$description || !$year ) die("Please fill in all required fields.");

    if (count($student_names) !== count($index_numbers)) {
        die("Member name / index mismatch.");
    }

    // ---------------------------------------------------------------
    // FILE UPLOAD HANDLING
    // ---------------------------------------------------------------
    $file_path = $existing_file;

    if (!empty($_FILES['project_file']['name'])) {

        if ($_FILES['project_file']['error'] === UPLOAD_ERR_OK) {

            $allowed_ext = ['pdf'];
            $file_name = $_FILES['project_file']['name'];
            $tmp = $_FILES['project_file']['tmp_name'];
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed_ext)) {
                die("Only PDF files allowed.");
            }

            $clean = preg_replace("/[^A-Za-z0-9_\-.]/", "_", $file_name);
            $new_name = time() . "_" . $clean;

            $upload_dir = __DIR__ . "/../uploads/projects/";
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            $target = $upload_dir . $new_name;

            if (!move_uploaded_file($tmp, $target)) {
                die("File upload failed.");
            }

            // DELETE OLD FILE
            if (!empty($existing_file)) {
                $old_file = $upload_dir . $existing_file;
                if (file_exists($old_file)) @unlink($old_file);
            }

            $file_path = $new_name;
        }
    }

    // ---------------------------------------------------------------
    // UPDATE PROJECTS TABLE
    // ---------------------------------------------------------------
    $stmt = $conn->prepare("
        UPDATE projects
        SET title = ?, synopsis = ?, year = ?, file_path = ?
        WHERE id = ? AND dep_id = ?
    ");

   $stmt->bind_param("ssisss", $title, $description, $year, $file_path, $project_id, $_SESSION['dep_id']);

    $stmt->execute();
    $stmt->close();

    // ---------------------------------------------------------------
    // UPDATE SUPERVISORS
    // ---------------------------------------------------------------
    $delSup = $conn->prepare("DELETE FROM project_supervisors WHERE project_id = ?");
    $delSup->bind_param("i", $project_id);
    $delSup->execute();
    $delSup->close();

    if (!empty($supervisors)) {
        $ps = $conn->prepare("INSERT INTO project_supervisors (project_id, supervisor_id) VALUES (?, ?)");
        foreach ($supervisors as $sid) {
            if ((int)$sid > 0) {
                $ps->bind_param("ii", $project_id, $sid);
                $ps->execute();
            }
        }
        $ps->close();
    }

    // ---------------------------------------------------------------
    // UPDATE MEMBERS
    // ---------------------------------------------------------------
    $delMem = $conn->prepare("DELETE FROM project_members WHERE project_id = ?");
    $delMem->bind_param("i", $project_id);
    $delMem->execute();
    $delMem->close();

    $mem = $conn->prepare("INSERT INTO project_members (project_id, student_name, index_number) VALUES (?, ?, ?)");
    foreach ($student_names as $i => $name) {
        $n = trim($student_names[$i]);
        $idx = trim($index_numbers[$i]);
        if ($n && $idx) {
            $mem->bind_param("iss", $project_id, $n, $idx);
            $mem->execute();
        }
    }
    $mem->close();

    // ---------------------------------------------------------------
    // UPDATE TAGS
    // ---------------------------------------------------------------
    $delTags = $conn->prepare("DELETE FROM project_tags WHERE project_id = ?");
    $delTags->bind_param("i", $project_id);
    $delTags->execute();
    $delTags->close();

    foreach ($tags as $tag) {
        if (!$tag) continue;

        // Check if tag exists
        $t = $conn->prepare("SELECT id FROM tags WHERE name = ?");
        $t->bind_param("s", $tag);
        $t->execute();
        $res = $t->get_result();

        if ($row = $res->fetch_assoc()) {
            $tag_id = $row['id'];
        } else {
            // Insert new tag
            $ins = $conn->prepare("INSERT INTO tags (name) VALUES (?)");
            $ins->bind_param("s", $tag);
            $ins->execute();
            $tag_id = $ins->insert_id;
            $ins->close();
        }
        $t->close();

        // Link tag to project
        $link = $conn->prepare("INSERT INTO project_tags (project_id, tag_id) VALUES (?, ?)");
        $link->bind_param("ii", $project_id, $tag_id);
        $link->execute();
        $link->close();
    }

    $conn->close();

    echo "<script>
            alert('Project updated successfully.');
            window.location.href = '../view_projects';
          </script>";
    exit();
}

echo "Invalid request";
?>
