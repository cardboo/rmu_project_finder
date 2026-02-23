<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../dashboard/");
    exit();
}

include "../datacon.php";

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dep_id = $_POST['dep_id'];
    $title = trim($_POST['project_title']);
    $description = trim($_POST['description']);
    $year = (int)$_POST['year'];
    // supervisors is an array of supervisor IDs (multiple)
    $supervisors = isset($_POST['supervisors']) ? (array)$_POST['supervisors'] : [];
    $student_names = $_POST['student_names'] ?? [];
    $index_numbers = $_POST['index_numbers'] ?? [];
    $tags_input = trim($_POST['tags']);
    $tags = array_filter(array_map('trim', explode(',', $tags_input)));

    // Basic validation
    if (empty($title) || empty($description) || empty($year)) {
        die("Please fill in all required fields.");
    }
    if (!is_array($student_names) || !is_array($index_numbers) || count($student_names) !== count($index_numbers)) {
        die("Project members data is invalid.");
    }

    // ---------- FILE UPLOAD ----------
    $file_path = null; // store only filename
    if (!empty($_FILES['project_file']['name'])) {
        $allowed_ext = ['pdf'];
        $file_name = $_FILES['project_file']['name'];
        $tmp = $_FILES['project_file']['tmp_name'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed_ext)) {
            die("Only PDF files allowed.");
        }

        // sanitize filename and create unique name
        $clean = preg_replace("/[^A-Za-z0-9_\-\.]/", "_", $file_name);
        $new_name = time() . '_' . $clean;
        $destination_dir = __DIR__ . '/../uploads/projects/';
        if (!is_dir($destination_dir)) mkdir($destination_dir, 0777, true);
        $destination = $destination_dir . $new_name;

        if (!move_uploaded_file($tmp, $destination)) {
            die("Failed to upload file.");
        }

        $file_path = $new_name; // SAVE only the filename
    }

    // ---------- INSERT PROJECT ----------
    $stmt = $conn->prepare("
        INSERT INTO projects (title, synopsis, year, dep_id,  file_path)
        VALUES (?, ?, ?, ?,?)
    ");
    if (!$stmt) die("Prepare failed: " . $conn->error);

    // bind nullable file_path
    $fp = $file_path ?? null;
    $stmt->bind_param("ssiss", $title, $description, $year, $dep_id,  $fp);

    if (!$stmt->execute()) die("Execute failed: " . $stmt->error);

    $project_id = $stmt->insert_id;
    $stmt->close();

    // ---------- INSERT MEMBERS ----------
    $stmtMember = $conn->prepare("INSERT INTO project_members (project_id, student_name, index_number) VALUES (?, ?, ?)");
    if (!$stmtMember) die("Prepare failed (members): " . $conn->error);

    for ($i = 0; $i < count($student_names); $i++) {
        $name = trim($student_names[$i]);
        $index = trim($index_numbers[$i]);
        if ($name === "" || $index === "") continue;
        $stmtMember->bind_param("iss", $project_id, $name, $index);
        $stmtMember->execute();
    }
    $stmtMember->close();

    // ---------- INSERT TAGS ----------
    foreach ($tags as $tag_name) {
        if ($tag_name === '') continue;
        // check tag exists
        $tagStmt = $conn->prepare("SELECT id FROM tags WHERE name = ?");
        $tagStmt->bind_param("s", $tag_name);
        $tagStmt->execute();
        $tagRes = $tagStmt->get_result();

        if ($tagRes->num_rows > 0) {
            $tag_id = $tagRes->fetch_assoc()['id'];
            $tagStmt->close();
        } else {
            $tagStmt->close();
            $insertTag = $conn->prepare("INSERT INTO tags (name) VALUES (?)");
            $insertTag->bind_param("s", $tag_name);
            $insertTag->execute();
            $tag_id = $insertTag->insert_id;
            $insertTag->close();
        }

        // link tag
        $link = $conn->prepare("INSERT INTO project_tags (project_id, tag_id) VALUES (?, ?)");
        $link->bind_param("ii", $project_id, $tag_id);
        $link->execute();
        $link->close();
    }

    // ---------- INSERT PROJECT_SUPERVISORS (bridge table) ----------
    if (!empty($supervisors)) {
        $insPS = $conn->prepare("INSERT INTO project_supervisors (project_id, supervisor_id) VALUES (?, ?)");
        foreach ($supervisors as $sid) {
            $sid = (int)$sid;
            if ($sid <= 0) continue;
            $insPS->bind_param("ii", $project_id, $sid);
            $insPS->execute();
        }
        $insPS->close();
    }

    $conn->close();

    echo "<script>
            alert('Project added successfully.');
            window.location.href = '../view_projects';
          </script>";
    exit();
} else {
    die("Invalid request method.");
}
