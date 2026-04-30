<?php
// Yeh script aapki poori website ke files parh kar saari Tailwind classes nikalegi
$classes = '';
// Main folder aur common folder ki files parho
$files = array_merge(glob("*.php"), glob("common/*.php"));

foreach($files as $file) {
    if(is_file($file)) {
        $content = file_get_contents($file);
        preg_match_all('/class=[\'"]([^\'"]+)[\'"]/i', $content, $matches);
        if(!empty($matches[1])) {
            foreach($matches[1] as $class_string) {
                $classes .= ' ' . $class_string;
            }
        }
    }
}
// Duplicate classes hata do
$unique_classes = implode(' ', array_unique(explode(' ', str_replace(["\n", "\r", "\t"], ' ', $classes))));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NURA - CSS Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { background: #faf9f6; font-family: sans-serif; text-align: center; padding-top: 100px; }</style>
</head>
<body>
    <h1 style="color:#000; font-size:28px; margin-bottom: 20px;">NURA CSS Generator</h1>
    <p style="color:green; font-weight:bold; margin-bottom: 30px;">✅ Website scanned successfully. CSS is ready!</p>
    
    <button onclick="downloadCSS()" style="background:#c49a6c; color:#fff; border:none; padding:15px 30px; font-size:18px; border-radius:30px; cursor:pointer; font-weight:bold; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        Download 'nura-style.css'
    </button>

    <div id="tailwind-classes" class="<?php echo $unique_classes; ?>" style="display:none;"></div>
    
    <script>
        function downloadCSS() {
            let css = '';
            document.querySelectorAll('style').forEach(s => {
                css += s.innerText + '\n';
            });
            
            if (css.length < 1000) {
                alert('Abhi CSS ban rahi hai, 2 second baad dubara click karein.');
                return;
            }
            
            const blob = new Blob([css], { type: 'text/css' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'nura-style.css';
            link.click();
            alert('File Download ho gayi! Ab isay cPanel mein upload karein.');
        }
    </script>
</body>
</html>