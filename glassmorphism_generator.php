<?php
session_start();
include 'db_connect.php';

$seo_title = "CSS Glassmorphism Generator | Free UI Tools";
$seo_description = "Easily create beautiful, frosted glass UI effects with our visual CSS Glassmorphism Generator. Customize blur, transparency, and color, then copy the CSS instantly.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'head_seo.php'; ?>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assest/css/style.css">
    <style>
        :root {
            --glass-bg: #F5F5F7;
            --glass-dark: #1D1D1F;
            --glass-gray: #86868B;
            --glass-blue: #0066CC;
            --glass-blue-hover: #0055AA;
            --shadow-sm: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--glass-bg);
            color: var(--glass-dark);
            min-height: 100vh;
        }

        .navbar-spacer { height: 90px; }

        .container {
            max-width: 1200px;
            margin: 2rem auto 4rem;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: flex-start;
        }

        /* Settings Area */
        .config-area {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            background: white;
            padding: 2.5rem;
            border-radius: 24px;
            box-shadow: var(--shadow-sm);
        }

        .config-header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 0.5rem;
        }
        
        .config-header p {
            color: var(--glass-gray);
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: flex;
            justify-content: space-between;
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 0.8rem;
            color: var(--glass-dark);
        }

        .val-display {
            color: var(--glass-blue);
            background: rgba(0, 102, 204, 0.1);
            padding: 0.2rem 0.6rem;
            border-radius: 6px;
            font-size: 0.85rem;
        }

        input[type="range"] {
            width: 100%;
            -webkit-appearance: none;
            height: 6px;
            background: #E8E8ED;
            border-radius: 5px;
            outline: none;
        }

        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--glass-blue);
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0, 102, 204, 0.3);
            transition: transform 0.1s;
        }

        input[type="range"]::-webkit-slider-thumb:hover {
            transform: scale(1.1);
        }

        .color-picker-wrapper {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: #F9F9FB;
            padding: 0.5rem;
            border-radius: 12px;
            border: 1px solid #E8E8ED;
        }

        input[type="color"] {
            -webkit-appearance: none;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            cursor: pointer;
            padding: 0;
            background: none;
        }
        
        input[type="color"]::-webkit-color-swatch-wrapper { padding: 0; }
        input[type="color"]::-webkit-color-swatch { border: none; border-radius: 8px; box-shadow: inset 0 0 0 1px rgba(0,0,0,0.1); }

        /* Output Area */
        .code-output {
            background: #1E1E1E;
            border-radius: 16px;
            padding: 1.5rem;
            position: relative;
            margin-top: 1rem;
        }

        .code-output pre {
            color: #E6E6E6;
            font-family: 'Space Mono', monospace;
            font-size: 0.9rem;
            line-height: 1.5;
            overflow-x: auto;
            margin: 0;
        }

        .copy-btn {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .copy-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Live Preview Area */
        .preview-area {
            border-radius: 24px;
            padding: 4rem 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: sticky;
            top: 110px;
            min-height: 600px;
            /* Premium animated mesh gradient background */
            background-color: #ff9a9e;
            background-image: 
                radial-gradient(at 0% 0%, #fecfef 0px, transparent 50%),
                radial-gradient(at 50% 0%, #ff9a9e 0px, transparent 50%),
                radial-gradient(at 100% 0%, #fecfef 0px, transparent 50%),
                radial-gradient(at 0% 50%, #fecfef 0px, transparent 50%),
                radial-gradient(at 100% 50%, #ff9a9e 0px, transparent 50%),
                radial-gradient(at 0% 100%, #fecfef 0px, transparent 50%),
                radial-gradient(at 50% 100%, #ff9a9e 0px, transparent 50%),
                radial-gradient(at 100% 100%, #fecfef 0px, transparent 50%);
            box-shadow: var(--shadow-md);
            overflow: hidden;
        }

        /* Decorative Background Elements to show off blur */
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(40px);
            opacity: 0.6;
            z-index: 1;
            animation: float 10s infinite ease-in-out alternate;
        }
        
        .blob-1 { width: 300px; height: 300px; background: #FF3B30; top: -50px; left: -50px; }
        .blob-2 { width: 400px; height: 400px; background: #5E5CE6; bottom: -100px; right: -100px; animation-delay: -5s; }

        @keyframes float {
            0% { transform: translateY(0) scale(1); }
            100% { transform: translateY(50px) scale(1.1); }
        }

        /* The Glass Card */
        .glass-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 400px;
            height: 300px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-radius: 24px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
            
            /* Initial Glass Effect defaults */
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            
            padding: 2rem;
            text-align: center;
            color: white;
            transition: all 0.1s;
        }

        .glass-card i {
            font-size: 4rem;
            margin-bottom: 1rem;
            text-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .glass-card h2 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .glass-card p {
            font-size: 1rem;
            opacity: 0.9;
            line-height: 1.5;
        }

        @media (max-width: 900px) {
            .container {
                grid-template-columns: 1fr;
            }
            .preview-area {
                position: static;
                min-height: 400px;
                order: -1;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="navbar-spacer"></div>

    <div class="container">
        <!-- Configuration Area -->
        <div class="config-area">
            <div class="config-header">
                <h1>Glassmorphism</h1>
                <p>Generate CSS for beautiful frosted glass effects.</p>
            </div>

            <div class="form-group">
                <label class="form-label">
                    Blur Value
                    <span class="val-display" id="blur-val">10px</span>
                </label>
                <input type="range" id="blur-slider" min="0" max="40" step="1" value="10">
            </div>

            <div class="form-group">
                <label class="form-label">
                    Background Transparency
                    <span class="val-display" id="opacity-val">0.25</span>
                </label>
                <input type="range" id="opacity-slider" min="0" max="1" step="0.01" value="0.25">
            </div>

            <div class="form-group">
                <label class="form-label">
                    Border Transparency
                    <span class="val-display" id="border-val">0.18</span>
                </label>
                <input type="range" id="border-slider" min="0" max="1" step="0.01" value="0.18">
            </div>

            <div class="form-group">
                <label class="form-label">Glass Color</label>
                <div class="color-picker-wrapper">
                    <input type="color" id="color-picker" value="#ffffff">
                    <span style="font-family: monospace; font-size: 0.9rem;" id="color-hex">#ffffff</span>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Border Color</label>
                <div class="color-picker-wrapper">
                    <input type="color" id="border-color-picker" value="#ffffff">
                    <span style="font-family: monospace; font-size: 0.9rem;" id="border-color-hex">#ffffff</span>
                </div>
            </div>

            <!-- Output CSS -->
            <div class="code-output">
                <button class="copy-btn" id="copy-btn"><i class="fas fa-copy"></i> Copy CSS</button>
                <pre id="css-output"></pre>
            </div>
        </div>

        <!-- Preview Area -->
        <div class="preview-area">
            <div class="blob blob-1"></div>
            <div class="blob blob-2"></div>
            
            <div class="glass-card" id="glass-card">
                <i class="fas fa-gem"></i>
                <h2>Glass Effect</h2>
                <p>Adjust the sliders to see the frosted glass update in real time.</p>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const blurSlider = document.getElementById('blur-slider');
            const opacitySlider = document.getElementById('opacity-slider');
            const borderSlider = document.getElementById('border-slider');
            const colorPicker = document.getElementById('color-picker');
            const borderColorPicker = document.getElementById('border-color-picker');
            
            const blurVal = document.getElementById('blur-val');
            const opacityVal = document.getElementById('opacity-val');
            const borderVal = document.getElementById('border-val');
            const colorHex = document.getElementById('color-hex');
            const borderColorHex = document.getElementById('border-color-hex');
            
            const glassCard = document.getElementById('glass-card');
            const cssOutput = document.getElementById('css-output');
            const copyBtn = document.getElementById('copy-btn');

            // Utility to convert hex to RGB
            function hexToRgb(hex) {
                const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
                return result ? {
                    r: parseInt(result[1], 16),
                    g: parseInt(result[2], 16),
                    b: parseInt(result[3], 16)
                } : { r: 255, g: 255, b: 255 };
            }

            function updateGlass() {
                const blur = blurSlider.value;
                const opacity = opacitySlider.value;
                const borderOpacity = borderSlider.value;
                const hexColor = colorPicker.value;
                const borderHexColor = borderColorPicker.value;
                
                // Update Value Labels
                blurVal.textContent = blur + 'px';
                opacityVal.textContent = opacity;
                borderVal.textContent = borderOpacity;
                colorHex.textContent = hexColor.toUpperCase();
                borderColorHex.textContent = borderHexColor.toUpperCase();

                const rgb = hexToRgb(hexColor);
                const bgRgba = `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, ${opacity})`;
                
                const borderRgb = hexToRgb(borderHexColor);
                const borderRgba = `rgba(${borderRgb.r}, ${borderRgb.g}, ${borderRgb.b}, ${borderOpacity})`;

                // Apply to Card
                glassCard.style.background = bgRgba;
                glassCard.style.setProperty('backdrop-filter', `blur(${blur}px)`);
                glassCard.style.setProperty('-webkit-backdrop-filter', `blur(${blur}px)`);
                glassCard.style.border = `1px solid ${borderRgba}`;

                // Generate CSS Code
                const cssText = 
`background: ${bgRgba};
border-radius: 16px;
box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
backdrop-filter: blur(${blur}px);
-webkit-backdrop-filter: blur(${blur}px);
border: 1px solid ${borderRgba};`;
                
                cssOutput.textContent = cssText;
            }

            // Event Listeners
            blurSlider.addEventListener('input', updateGlass);
            opacitySlider.addEventListener('input', updateGlass);
            borderSlider.addEventListener('input', updateGlass);
            colorPicker.addEventListener('input', updateGlass);
            borderColorPicker.addEventListener('input', updateGlass);

            // Initial render
            updateGlass();

            // Copy to Clipboard
            copyBtn.addEventListener('click', () => {
                navigator.clipboard.writeText(cssOutput.textContent).then(() => {
                    const originalText = copyBtn.innerHTML;
                    copyBtn.innerHTML = '<i class="fas fa-check"></i> Copied!';
                    setTimeout(() => {
                        copyBtn.innerHTML = originalText;
                    }, 2000);
                });
            });
        });
    </script>
</body>
</html>
