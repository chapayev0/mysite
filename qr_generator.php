<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';
?>
<?php
$seo_title = "Premium QR Code Generator | Free Custom QR Codes";
$seo_description = "Create beautiful, high-resolution QR codes for free. Customize colors, styles, and embed your own center logo directly from your browser.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'head_seo.php'; ?>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assest/css/style.css">
    <script type="text/javascript" src="https://unpkg.com/qr-code-styling@1.5.0/lib/qr-code-styling.js"></script>
    <style>
        :root {
            --qr-bg: #F5F5F7;
            --qr-dark: #1D1D1F;
            --qr-gray: #86868B;
            --qr-light: #FFFFFF;
            --qr-blue: #0066CC;
            --qr-blue-hover: #0055AA;
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.5);
            --shadow-sm: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--qr-bg);
            background-image: 
                radial-gradient(at 0% 0%, rgba(0, 102, 204, 0.05) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(124, 58, 237, 0.05) 0px, transparent 50%);
            background-attachment: fixed;
            color: var(--qr-dark);
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

        /* Left Side: Preview Area */
        .preview-area {
            background: #E8E8ED;
            border-radius: 24px;
            padding: 4rem 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: sticky;
            top: 110px;
            box-shadow: inset 0 2px 10px rgba(0,0,0,0.05);
            min-height: 500px;
        }

        .qr-wrapper {
            background: white;
            padding: 1.5rem;
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            margin-bottom: 2rem;
            transition: transform 0.3s ease;
        }
        
        .qr-wrapper:hover {
            transform: scale(1.02);
        }

        .scan-hint {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.8rem 1.5rem;
            background: #333336;
            color: white;
            border-radius: 30px;
            font-size: 0.95rem;
            font-weight: 500;
        }

        /* Right Side: Configuration Panel */
        .config-area {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .config-header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 0.5rem;
        }

        /* Tabs */
        .tabs {
            display: flex;
            gap: 1rem;
            border-bottom: 2px solid #E8E8ED;
            padding-bottom: 0.5rem;
            overflow-x: auto;
            scrollbar-width: none;
        }
        .tabs::-webkit-scrollbar { display: none; }

        .tab-btn {
            background: none;
            border: none;
            padding: 0.5rem 1rem;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 600;
            color: var(--qr-gray);
            cursor: pointer;
            border-radius: 20px;
            transition: all 0.3s;
            white-space: nowrap;
        }

        .tab-btn:hover {
            color: var(--qr-dark);
            background: rgba(0,0,0,0.05);
        }

        .tab-btn.active {
            color: var(--qr-dark);
            background: var(--glass-bg);
            box-shadow: var(--shadow-sm);
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Form Groups */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--qr-dark);
        }

        .form-input, .form-select {
            width: 100%;
            padding: 1rem 1.2rem;
            border-radius: 12px;
            border: 1px solid #E8E8ED;
            background: white;
            font-family: inherit;
            font-size: 1rem;
            color: var(--qr-dark);
            transition: all 0.3s;
        }

        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: var(--qr-blue);
            box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
        }

        .hint-text {
            font-size: 0.8rem;
            color: var(--qr-gray);
            margin-top: 0.5rem;
        }

        /* Color Pickers Grid */
        .color-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 1rem;
        }

        .color-picker-wrapper {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.5rem;
            background: white;
            border: 1px solid #E8E8ED;
            border-radius: 10px;
        }

        .color-picker {
            width: 30px;
            height: 30px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            padding: 0;
            background: none;
        }

        .color-picker::-webkit-color-swatch-wrapper { padding: 0; }
        .color-picker::-webkit-color-swatch { border: none; border-radius: 6px; box-shadow: inset 0 0 0 1px rgba(0,0,0,0.1); }

        /* Style Selectors Grid */
        .style-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 0.8rem;
            margin-bottom: 1.5rem;
        }

        .style-btn {
            background: white;
            border: 1px solid #E8E8ED;
            padding: 0.8rem;
            border-radius: 10px;
            cursor: pointer;
            text-align: center;
            font-family: inherit;
            font-weight: 500;
            transition: all 0.2s;
        }

        .style-btn:hover {
            border-color: #C0C0C0;
            background: #F9F9FB;
        }

        .style-btn.active {
            border-color: var(--qr-blue);
            background: rgba(0, 102, 204, 0.05);
            color: var(--qr-blue);
        }

        /* Actions */
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #E8E8ED;
        }

        .btn-primary {
            background: var(--qr-blue);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 30px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary:hover {
            background: var(--qr-blue-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 102, 204, 0.4);
        }

        .btn-secondary {
            background: white;
            color: var(--qr-dark);
            border: 1px solid #E8E8ED;
            padding: 1rem 1.5rem;
            border-radius: 30px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-secondary:hover {
            background: #F5F5F7;
            border-color: #D1D1D6;
        }

        @media (max-width: 900px) {
            .container {
                grid-template-columns: 1fr;
            }
            .preview-area {
                position: static;
                min-height: 400px;
                order: -1; /* Move preview above form on mobile */
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
                <h1>Generate QR code</h1>
            </div>

            <!-- Tabs Navigation -->
            <div class="tabs">
                <button class="tab-btn active" data-tab="content">Content</button>
                <button class="tab-btn" data-tab="styles">Styles & Colors</button>
                <button class="tab-btn" data-tab="logo">Logo / Image</button>
            </div>

            <!-- Tab: Content -->
            <div class="tab-content active" id="tab-content">
                <div class="form-group">
                    <label class="form-label">Enter or paste URL / Text:</label>
                    <input type="text" id="qr-data" class="form-input" value="https://example.com" placeholder="https://...">
                    <p class="hint-text">Your QR code will open this URL or show this text.</p>
                </div>
            </div>

            <!-- Tab: Styles & Colors -->
            <div class="tab-content" id="tab-styles">
                <div class="form-group">
                    <label class="form-label">Background & Dots Color</label>
                    <div class="color-grid">
                        <div class="color-picker-wrapper">
                            <input type="color" id="bg-color" class="color-picker" value="#ffffff">
                            <span>Background</span>
                        </div>
                        <div class="color-picker-wrapper">
                            <input type="color" id="dots-color" class="color-picker" value="#000000">
                            <span>Dots</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Marker Colors</label>
                    <div class="color-grid">
                        <div class="color-picker-wrapper">
                            <input type="color" id="marker-border-color" class="color-picker" value="#000000">
                            <span>Border</span>
                        </div>
                        <div class="color-picker-wrapper">
                            <input type="color" id="marker-center-color" class="color-picker" value="#000000">
                            <span>Center</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Dot Style</label>
                    <div class="style-grid" id="dot-style-grid">
                        <button class="style-btn active" data-val="square">Square</button>
                        <button class="style-btn" data-val="dots">Dots</button>
                        <button class="style-btn" data-val="rounded">Rounded</button>
                        <button class="style-btn" data-val="extra-rounded">Extra Round</button>
                        <button class="style-btn" data-val="classy">Classy</button>
                        <button class="style-btn" data-val="classy-rounded">Classy Round</button>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Marker Border Style</label>
                    <div class="style-grid" id="marker-border-grid">
                        <button class="style-btn active" data-val="square">Square</button>
                        <button class="style-btn" data-val="dot">Dot</button>
                        <button class="style-btn" data-val="extra-rounded">Extra Round</button>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Marker Center Style</label>
                    <div class="style-grid" id="marker-center-grid">
                        <button class="style-btn active" data-val="square">Square</button>
                        <button class="style-btn" data-val="dot">Dot</button>
                    </div>
                </div>
            </div>

            <!-- Tab: Logo -->
            <div class="tab-content" id="tab-logo">
                <div class="form-group">
                    <label class="form-label">Upload Image / Logo (Optional)</label>
                    <input type="file" id="logo-file" class="form-input" accept="image/png, image/jpeg, image/webp, image/svg+xml" style="padding: 0.7rem 1.2rem; margin-bottom: 0.5rem;">
                    
                    <div style="text-align: center; margin: 1rem 0;">
                        <span style="color: var(--qr-gray); font-size: 0.85rem; font-weight: 600;">- OR PASTE URL -</span>
                    </div>

                    <input type="text" id="logo-url" class="form-input" placeholder="https://example.com/logo.png">
                    <p class="hint-text">Upload an image or paste a direct link. Leave blank for no logo.</p>
                </div>
                <div class="form-group">
                    <label class="form-label">Image Margin</label>
                    <input type="range" id="logo-margin" min="0" max="20" value="0" style="width:100%">
                </div>
            </div>

            <!-- Actions -->
            <div class="action-buttons">
                <button class="btn-primary" id="download-btn">
                    <i class="fas fa-download"></i> Download PNG
                </button>
                <select id="file-format" class="btn-secondary" style="width:auto; padding-right: 2.5rem; appearance: auto;">
                    <option value="png">PNG</option>
                    <option value="svg">SVG</option>
                    <option value="jpeg">JPEG</option>
                    <option value="webp">WEBP</option>
                </select>
            </div>
        </div>

        <!-- Preview Area -->
        <div class="preview-area">
            <div class="qr-wrapper" id="qr-code"></div>
            <div class="scan-hint">
                <i class="fas fa-info-circle"></i> Scan your QR code to test it out
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize QR Code Styling
            const qrCode = new QRCodeStyling({
                width: 300,
                height: 300,
                margin: 24, // Proportional white border for the 300x300 preview
                type: "svg",
                data: "https://example.com",
                image: "",
                dotsOptions: {
                    color: "#000000",
                    type: "square"
                },
                backgroundOptions: {
                    color: "#ffffff",
                },
                imageOptions: {
                    crossOrigin: "anonymous",
                    margin: 0
                },
                cornersSquareOptions: {
                    color: "#000000",
                    type: "square"
                },
                cornersDotOptions: {
                    color: "#000000",
                    type: "square"
                }
            });

            // Append to DOM
            qrCode.append(document.getElementById("qr-code"));

            // Element References
            const dataInput = document.getElementById('qr-data');
            const bgColorInput = document.getElementById('bg-color');
            const dotsColorInput = document.getElementById('dots-color');
            const markerBorderColorInput = document.getElementById('marker-border-color');
            const markerCenterColorInput = document.getElementById('marker-center-color');
            const logoUrlInput = document.getElementById('logo-url');
            const logoFileInput = document.getElementById('logo-file');
            const logoMarginInput = document.getElementById('logo-margin');
            
            const downloadBtn = document.getElementById('download-btn');

            let currentLogoDataUrl = "";
            const fileFormatSelect = document.getElementById('file-format');

            // Current Styles State
            let currentDotStyle = 'square';
            let currentMarkerBorderStyle = 'square';
            let currentMarkerCenterStyle = 'square';

            // Update Function
            function updateQRCode() {
                const data = dataInput.value || "https://example.com";
                const bgColor = bgColorInput.value;
                const dotsColor = dotsColorInput.value;
                const markerBorderColor = markerBorderColorInput.value;
                const markerCenterColor = markerCenterColorInput.value;
                const logoUrl = currentLogoDataUrl || logoUrlInput.value;
                const logoMargin = parseInt(logoMarginInput.value) || 0;

                qrCode.update({
                    width: 300,
                    height: 300,
                    margin: 24,
                    data: data,
                    image: logoUrl,
                    backgroundOptions: { color: bgColor },
                    dotsOptions: { color: dotsColor, type: currentDotStyle },
                    cornersSquareOptions: { color: markerBorderColor, type: currentMarkerBorderStyle },
                    cornersDotOptions: { color: markerCenterColor, type: currentMarkerCenterStyle },
                    imageOptions: { crossOrigin: "anonymous", margin: logoMargin }
                });
            }

            // Event Listeners for Inputs
            dataInput.addEventListener('input', updateQRCode);
            bgColorInput.addEventListener('input', updateQRCode);
            dotsColorInput.addEventListener('input', updateQRCode);
            markerBorderColorInput.addEventListener('input', updateQRCode);
            markerCenterColorInput.addEventListener('input', updateQRCode);
            
            logoUrlInput.addEventListener('input', () => {
                if (logoUrlInput.value.trim() !== '') {
                    logoFileInput.value = '';
                    currentLogoDataUrl = "";
                }
                updateQRCode();
            });

            logoFileInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        currentLogoDataUrl = event.target.result;
                        logoUrlInput.value = '';
                        updateQRCode();
                    };
                    reader.readAsDataURL(file);
                } else {
                    currentLogoDataUrl = "";
                    updateQRCode();
                }
            });

            logoMarginInput.addEventListener('input', updateQRCode);

            // Setup Style Buttons (Radio-like behavior)
            function setupStyleGrid(gridId, updateVarCb) {
                const grid = document.getElementById(gridId);
                const buttons = grid.querySelectorAll('.style-btn');
                buttons.forEach(btn => {
                    btn.addEventListener('click', () => {
                        // Remove active class from all
                        buttons.forEach(b => b.classList.remove('active'));
                        // Add active class to clicked
                        btn.classList.add('active');
                        // Update state and refresh QR
                        updateVarCb(btn.getAttribute('data-val'));
                        updateQRCode();
                    });
                });
            }

            setupStyleGrid('dot-style-grid', (val) => currentDotStyle = val);
            setupStyleGrid('marker-border-grid', (val) => currentMarkerBorderStyle = val);
            setupStyleGrid('marker-center-grid', (val) => currentMarkerCenterStyle = val);

            // Download Functionality
            downloadBtn.addEventListener('click', () => {
                const format = fileFormatSelect.value;
                
                // Create a separate, high-resolution instance specifically for downloading
                const data = dataInput.value || "https://example.com";
                const bgColor = bgColorInput.value;
                const dotsColor = dotsColorInput.value;
                const markerBorderColor = markerBorderColorInput.value;
                const markerCenterColor = markerCenterColorInput.value;
                const logoUrl = currentLogoDataUrl || logoUrlInput.value;
                const logoMargin = parseInt(logoMarginInput.value) || 0;

                const downloadQrCode = new QRCodeStyling({
                    width: 500,
                    height: 500,
                    margin: 40, // Extra large white space for the 500x500 download
                    type: format === 'svg' ? 'svg' : 'canvas',
                    data: data,
                    image: logoUrl,
                    backgroundOptions: { color: bgColor },
                    dotsOptions: { color: dotsColor, type: currentDotStyle },
                    cornersSquareOptions: { color: markerBorderColor, type: currentMarkerBorderStyle },
                    cornersDotOptions: { color: markerCenterColor, type: currentMarkerCenterStyle },
                    imageOptions: { crossOrigin: "anonymous", margin: logoMargin }
                });

                downloadQrCode.download({ name: "qr-code", extension: format });
            });

            fileFormatSelect.addEventListener('change', () => {
                const format = fileFormatSelect.value;
                downloadBtn.innerHTML = `<i class="fas fa-download"></i> Download ${format.toUpperCase()}`;
            });

            // Tabs Logic
            const tabBtns = document.querySelectorAll('.tab-btn');
            const tabContents = document.querySelectorAll('.tab-content');

            tabBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    // Remove active from all
                    tabBtns.forEach(b => b.classList.remove('active'));
                    tabContents.forEach(c => c.classList.remove('active'));

                    // Add active to clicked
                    btn.classList.add('active');
                    const targetId = `tab-${btn.getAttribute('data-tab')}`;
                    document.getElementById(targetId).classList.add('active');
                });
            });

        });
    </script>
</body>
</html>
