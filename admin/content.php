<?php 
require_once '../common/config.php'; 

// Delete File Logic
if(isset($_GET['delete_file'])) {
    $file = '../assets/uploads/' . basename($_GET['delete_file']);
    if(file_exists($file)) { unlink($file); }
    header("Location: content.php"); exit();
}

$active_tab = 'content';
include 'common/header.php'; 
?>

<div class="flex justify-between items-end mb-4">
    <h1 class="text-2xl font-bold text-[#202223]">Content & Files</h1>
</div>

<div class="shopify-card p-6">
    <h3 class="font-bold mb-4 border-b pb-2">Uploaded Media</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <?php 
        $dir = '../assets/uploads/';
        if(is_dir($dir)) {
            $files = array_diff(scandir($dir), array('.', '..'));
            foreach($files as $file) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                    echo '<div class="relative group rounded-lg overflow-hidden border border-gray-200 aspect-square">
                            <img src="'.$dir.$file.'" class="w-full h-full object-cover">
                            <a href="?delete_file='.$file.'" onclick="return confirm(\'Delete image?\')" class="absolute inset-0 bg-black/60 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition">Delete</a>
                          </div>';
                }
            }
            if(empty($files)) { echo "<p class='col-span-full text-gray-500'>No media uploaded yet.</p>"; }
        }
        ?>
    </div>
</div>

<?php include 'common/footer.php'; ?>