<?php
if(isset($_POST['imageData'])){
    $imageData = $_POST['imageData'];
    
    // Remove the data prefix
    $imageData = str_replace('data:image/png;base64,', '', $imageData);
    
    // Decode the image data
    $decodedImage = base64_decode($imageData);
    
    // Define the file path where you want to save the image
    $imageFilePath = '/wm/mvs/wm_web/var/apache2/2.4/htdocs/web_apps/plot_macro/public/images/chart_time_series.png';
    
    // Save the image
    file_put_contents($imageFilePath, $decodedImage);
    
    echo 'Image saved successfully!';
} else {
    echo 'No image data received';
}
?>
