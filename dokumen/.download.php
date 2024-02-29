<?php
// Initialize a file URL to the variable
$nama_file = "dataset.csv";
// Path lengkap menuju file
$path_ke_file = 'http://localhost/templatemo/dataset/' . $nama_file;

// Use basename() function to return the base name of the file
$file_name = basename($path_ke_file);

// Set the suggested file name for download
$suggested_file_name = $nama_file;

// Use header() function to set Content-Disposition header
header("Content-Disposition: attachment; filename=\"" . $suggested_file_name . "\"");

// Use file_get_contents() function to get the file
// from the URL and use file_put_contents() function to
// save the file using the base name
if (file_put_contents($file_name, file_get_contents($path_ke_file))) {
    echo "File downloaded successfully";
} else {
    echo "File downloading failed.";
}
?>

<?php

if (isset($_POST["download"])) {
  // Get parameters
  $file = urldecode($_POST["download"]); // Decode URL-encoded string

  /* Check if the file name includes illegal characters
   like "../" using the regular expression */
  if (preg_match('/^[^.][-a-z0-9_.]+[a-z]$/i', $file)) {
    $filepath = "http://localhost/templatemo/dataset/" . $file;

    // Process download
    if (file_exists($filepath)) {
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize($filepath));
      flush(); // Flush system output buffer
      readfile($filepath);
      die();
    } else {
      http_response_code(404);
      die();
    }
  } else {
    die("Invalid file name!");
  }
}

?>