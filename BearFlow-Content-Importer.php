<?php
/*
Plugin Name: BearFlow Content Importer  
Description: Content Importer Plugin 
Version: 1.0.0-alpha
Author: Grey Bear Enterprises
Author URI: https://www.greybearenterprises.com
*/

add_action('admin_menu', 'bearflow_admin_actions');



function bearflow_admin_actions() {
    add_menu_page('BearFlow Content Importer', 'BearFlow Content Importer', 1, 'bearflow_adminpage', 'bearflow_adminpage', plugin_dir_url( __FILE__ ) . 'images/wordpress-menu-icon.png', 20);
}

function bearflow_adminpage() {
    ob_start();


    ob_end_flush();
    return 1;
}


// Add the form shortcode
add_shortcode('csv_upload_form', 'csv_upload_form_shortcode');
function csv_upload_form_shortcode() {
    ob_start();
    csv_upload_form_render();
    return ob_get_clean();
}

// Render the form HTML
function csv_upload_form_render() {
    ?>
    <form method="post" enctype="multipart/form-data">
        <label for="csv_file">CSV File:</label>
        <input type="file" name="csv_file" id="csv_file" accept=".csv" required>
        <br><br>
        <input type="submit" name="csv_submit" value="Upload">
    </form>
    <?php
}

// Handle form submission
add_action('init', 'csv_upload_handle_form_submission');
function csv_upload_handle_form_submission() {
    if (isset($_POST['csv_submit'])) {
        if (isset($_FILES['csv_file'])) {
            $file = $_FILES['csv_file'];

            // Verify file type
            $file_type = wp_check_filetype($file['name'], null);
            if ($file_type['ext'] !== 'csv') {
                echo 'Invalid file format. Please upload a CSV file.';
                return;
            }

            // Handle file upload
            $upload_dir = wp_upload_dir();
            $target_file = trailingslashit($upload_dir['path']) . basename($file['name']);

            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                // File uploaded successfully, process and display its content
                $csv_content = process_csv_file($target_file);
                if ($csv_content) {
                    display_csv_content($csv_content);
                } else {
                    echo 'Error processing the CSV file.';
                }
            } else {
                echo 'Error uploading file. Please try again.';
            }
        }
    }
}

// Process the CSV file and return its content
function process_csv_file($file_path) {
    $csv_content = array();

    if (($handle = fopen($file_path, 'r')) !== false) {
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            $csv_content[] = $data;
        }
        fclose($handle);

        


        return $csv_content;
    }

    return false;
}

// Display the CSV content in a table
function display_csv_content($csv_content) {
    ?>
    <table>
        <thead>
            <tr>
                <?php foreach ($csv_content[0] as $header) : ?>
                    <th><?php echo esc_html($header); ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 1; $i < count($csv_content); $i++) : 
                
                    $row = $csv_content[$i];
                    $post_title = $row[0];
                    $fname = $row[1];
                    $lname = $row[2];
                    $job_title  = $row[3];
                    $company = $row[4];
                    $city = $row[5];
                    $country  = $row[6];
                    $whatsapp  = $row[7];
                    $mobile  = $row[8];
                    $email  = $row[9];
                    $facebook = $row[10];
                    $social_lnkdin  = $row[11];
                    $instagram  = $row[12];
                    echo $post_title;

                    $user_id = get_current_user_id();
 
    $post_arr = array(
        'post_title'   => esc_html($post_title),
        'post_content' => '',
        'post_status'  => 'publish',
        'post_author'  => $user_id,
        'post_type'	   => 'contacts',
        'meta_input'   => array(
            'fname' => $fname,
            'lname'   => $lname,
            'job_title' => $job_title,
            'company' => $company,
            'city' => $city,
            'country' => $country,
            'whatsapp'  => $whatsapp,
            'mobile' => $mobile,
            'email' => $email,
            'facebook' => $facebook,
            'social_lnkdin' => $social_lnkdin,
            'instagram' => $instagram,
            '_fname' => 'field_6469373d6398f',
            '_lname' => 'field_6469376363990',
            '_job_title' => 'field_6469376b63991',
            '_company' => 'field_6469377c63992',
            '_city' => 'field_646937a063993',
            '_country' => 'field_646937ab63994',
            '_whatsapp' => 'field_646937fe63995',
            '_mobile' => 'field_6469380a63996',
            '_email' => 'field_6469382063997',
            '_facebook' => 'field_6469382a63998',
            '_social_lnkdin' => 'field_6469384763999',
            '_instagram' => 'field_6469386b6399a',
            'others'  => '',
            '_others' => 'field_646938806399b'            
        ),
    );
            
    $post = wp_insert_post($post_arr);               
                ?>
                
            <?php endfor; ?>
        </tbody>
    </table>
    <?php
}
