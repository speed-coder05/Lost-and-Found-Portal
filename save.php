<?php
require 'auth.php';
requireLogin();

$name        = $conn->real_escape_string(strip_tags(trim($_POST['name']        ?? '')));
$description = $conn->real_escape_string(strip_tags(trim($_POST['description'] ?? '')));
$location    = $conn->real_escape_string(strip_tags(trim($_POST['location']    ?? '')));
$email       = $conn->real_escape_string(filter_var(trim($_POST['email']       ?? ''), FILTER_SANITIZE_EMAIL));
$phone       = $conn->real_escape_string(strip_tags(trim($_POST['phone']       ?? '')));
$tag         = ($_POST['tag'] ?? 'lost') === 'found' ? 'found' : 'lost';
$user_id     = (int)$_SESSION['user_id'];

// Handle image upload
$image = '';
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $allowed = ['image/jpeg','image/png','image/webp','image/gif'];
    $finfo   = finfo_open(FILEINFO_MIME_TYPE);
    $mime    = finfo_file($finfo, $_FILES['image']['tmp_name']);
    finfo_close($finfo);
    if (in_array($mime, $allowed)) {
        $ext   = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = uniqid('item_', true) . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $image);
    }
}

//  MATCH LOGIC 
// Opposite tag to what was just posted
$opposite_tag = ($tag === 'lost') ? 'found' : 'lost';

// Look for any existing item with the same name (case-insensitive) and opposite tag
$match = $conn->query(
    "SELECT id FROM items
     WHERE tag = '$opposite_tag'
       AND LOWER(name) = LOWER('$name')
     ORDER BY id ASC
     LIMIT 1"
);

$matched = false;
if ($match && $match->num_rows > 0) {
    $match_row = $match->fetch_assoc();
    // Delete the matched item (cancel it out)
    $conn->query("DELETE FROM items WHERE id = " . (int)$match_row['id']);
    $matched = true;
    // Don't insert the new one either - both are resolved
} else {
    // No match - insert normally
    $conn->query(
        "INSERT INTO items (name, description, location, email, phone, image, tag, user_id)
         VALUES ('$name','$description','$location','$email','$phone','$image','$tag','$user_id')"
    );
}

$param = $matched ? 'matched=1' : 'success=1';
header("Location: index.php?{$param}#report");
exit;
