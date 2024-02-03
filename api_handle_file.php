<?php
/*
Plugin Name: File Upload API
Description: Custom REST API for file uploads.
Version: 1.0
*/

// Register custom REST API endpoint for file uploads
add_action('rest_api_init', 'register_file_upload_api');

function register_file_upload_api() {
    register_rest_route('file-upload-api/v1', '/upload/', array(
        'methods'  => 'POST',
        'callback' => 'handle_file_upload',
    ));
}

// Callback function to handle file uploads
function handle_file_upload($request) {
    $response = array();

    // Check if the file is provided in the request
    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];

        // Set the upload directory
        $upload_dir = wp_upload_dir();
        $subfolder = 'uploaded_files/';
        $upload_path = $upload_dir['basedir'] . '/' . $subfolder;

        // Create the subfolder if it doesn't exist
        if (!file_exists($upload_path)) {
            wp_mkdir_p($upload_path);
        }

        // Set the file name
        $file_name = sanitize_file_name($file['name']);
        $file_path = $upload_path . $file_name;

        // Move the uploaded file to the specified subfolder
        $moved = move_uploaded_file($file['tmp_name'], $file_path);

        if ($moved) {
            $response['success'] = true;
            $response['message'] = 'File uploaded successfully.';
            $response['file_url'] = $upload_dir['baseurl'] . '/' . $subfolder . $file_name;
        } else {
            $response['success'] = false;
            $response['message'] = 'Failed to upload the file.';
        }
    } else {
        $response['success'] = false;
        $response['message'] = 'File not provided in the request.';
    }

    return rest_ensure_response($response);
}
