<?php
include 'db_connect.php';

// Adjust these to match existing class folders in classes/
$samples = [
    ['title'=>'Sample Theory 1','description'=>'Intro to topic A','filename'=>'theory1.pdf','filepath'=>'classes/g6/theory/theory1.pdf','category'=>'theory','grade'=>'g6'],
    ['title'=>'Sample Tute 1','description'=>'Tute for topic A','filename'=>'tute1.pdf','filepath'=>'classes/g6/tute/tute1.pdf','category'=>'tute','grade'=>'g6'],
    ['title'=>'Sample Paper 1','description'=>'Exam paper','filename'=>'paper1.pdf','filepath'=>'classes/g6/paper/paper1.pdf','category'=>'paper','grade'=>'g6'],
];

foreach ($samples as $s) {
    // only insert if not exists (by filepath)
    $chk = $conn->prepare('SELECT id FROM resources WHERE filepath = ?');
    $chk->bind_param('s', $s['filepath']);
    $chk->execute();
    $chk->store_result();
    if ($chk->num_rows > 0) {
        echo "Already exists: {$s['filepath']}\n";
        $chk->close();
        continue;
    }
    $chk->close();

    $stmt = $conn->prepare('INSERT INTO resources (title, description, filename, filepath, category, grade, uploaded_by) VALUES (?, ?, ?, ?, ?, ?, NULL)');
    $stmt->bind_param('ssssss', $s['title'], $s['description'], $s['filename'], $s['filepath'], $s['category'], $s['grade']);
    if ($stmt->execute()) {
        echo "Inserted: {$s['filepath']} (id={$stmt->insert_id})\n";
    } else {
        echo "Error inserting {$s['filepath']}: " . $conn->error . "\n";
    }
    $stmt->close();
}

$res = $conn->query('SELECT id,title,category,grade,filepath,created_at FROM resources ORDER BY id DESC LIMIT 10');
if ($res) {
    echo "\nRecent resources:\n";
    while ($r = $res->fetch_assoc()) {
        echo "id={$r['id']} title={$r['title']} category={$r['category']} grade={$r['grade']} file={$r['filepath']} created={$r['created_at']}\n";
    }
}

$conn->close();
?>
