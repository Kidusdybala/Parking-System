#!/usr/bin/env python3
"""
Simple script to create a favicon with a white "P" on black background
"""

try:
    from PIL import Image, ImageDraw, ImageFont
    import os
    
    # Create a 32x32 image with black background
    size = 32
    img = Image.new('RGB', (size, size), color='black')
    draw = ImageDraw.Draw(img)
    
    # Try to use a system font, fallback to default
    try:
        font = ImageFont.truetype("arial.ttf", 20)
    except:
        try:
            font = ImageFont.truetype("C:/Windows/Fonts/arial.ttf", 20)
        except:
            font = ImageFont.load_default()
    
    # Calculate text position to center it
    text = "P"
    bbox = draw.textbbox((0, 0), text, font=font)
    text_width = bbox[2] - bbox[0]
    text_height = bbox[3] - bbox[1]
    
    x = (size - text_width) // 2
    y = (size - text_height) // 2 - 2  # Adjust slightly up
    
    # Draw the white "P"
    draw.text((x, y), text, fill='white', font=font)
    
    # Save as PNG first
    img.save('public/favicon-32.png')
    
    # Create 16x16 version
    img_16 = img.resize((16, 16), Image.Resampling.LANCZOS)
    img_16.save('public/favicon-16.png')
    
    # Create ICO file with multiple sizes
    img.save('public/favicon.ico', format='ICO', sizes=[(16, 16), (32, 32)])
    
    print("Favicon files created successfully!")
    print("- favicon.ico (16x16 and 32x32)")
    print("- favicon-16.png")
    print("- favicon-32.png")
    
except ImportError:
    print("PIL (Pillow) not installed. Please install it with: pip install Pillow")
    print("Alternatively, you can use the online favicon generator at https://realfavicongenerator.net/")
    
    # Create a simple HTML-based solution
    html_content = '''<!DOCTYPE html>
<html>
<head>
    <style>
        .favicon { 
            width: 32px; 
            height: 32px; 
            background: black; 
            color: white; 
            font: bold 20px Arial; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
        }
    </style>
</head>
<body>
    <div class="favicon">P</div>
    <p>Right-click the black square above and save as image, then convert to ICO format.</p>
</body>
</html>'''
    
    with open('public/favicon-manual.html', 'w') as f:
        f.write(html_content)
    
    print("Created favicon-manual.html for manual conversion")
