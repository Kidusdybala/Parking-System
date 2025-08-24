// Simple Node.js script to create a basic favicon
const fs = require('fs');

// Create a minimal ICO file header for 16x16 icon
const icoHeader = Buffer.from([
    0x00, 0x00, // Reserved
    0x01, 0x00, // Type (1 = ICO)
    0x01, 0x00, // Number of images
    // Image directory entry
    0x10,       // Width (16)
    0x10,       // Height (16)
    0x00,       // Color count (0 = no palette)
    0x00,       // Reserved
    0x01, 0x00, // Color planes
    0x20, 0x00, // Bits per pixel (32)
    0x00, 0x04, 0x00, 0x00, // Image size (1024 bytes)
    0x16, 0x00, 0x00, 0x00  // Image offset (22 bytes)
]);

// Create bitmap header
const bmpHeader = Buffer.from([
    0x28, 0x00, 0x00, 0x00, // Header size (40)
    0x10, 0x00, 0x00, 0x00, // Width (16)
    0x20, 0x00, 0x00, 0x00, // Height (32 - includes AND mask)
    0x01, 0x00,             // Planes (1)
    0x20, 0x00,             // Bits per pixel (32)
    0x00, 0x00, 0x00, 0x00, // Compression (0 = none)
    0x00, 0x04, 0x00, 0x00, // Image size (1024)
    0x00, 0x00, 0x00, 0x00, // X pixels per meter
    0x00, 0x00, 0x00, 0x00, // Y pixels per meter
    0x00, 0x00, 0x00, 0x00, // Colors used
    0x00, 0x00, 0x00, 0x00  // Important colors
]);

// Create 16x16 black image with white "P"
const imageData = Buffer.alloc(16 * 16 * 4); // 4 bytes per pixel (BGRA)

// Fill with black pixels (alpha = 255)
for (let i = 0; i < 16 * 16; i++) {
    const offset = i * 4;
    imageData[offset] = 0x00;     // Blue
    imageData[offset + 1] = 0x00; // Green
    imageData[offset + 2] = 0x00; // Red
    imageData[offset + 3] = 0xFF; // Alpha
}

// Draw a simple "P" pattern with white pixels
const pPattern = [
    [4,2], [4,3], [4,4], [4,5], [4,6], [4,7], [4,8], [4,9], [4,10], [4,11], [4,12], [4,13],
    [5,2], [6,2], [7,2], [8,2], [9,2],
    [5,6], [6,6], [7,6], [8,6],
    [5,13], [6,13], [7,13], [8,13], [9,13],
    [9,3], [9,4], [9,5],
    [9,7], [9,8], [9,9]
];

pPattern.forEach(([x, y]) => {
    if (x < 16 && y < 16) {
        const offset = (y * 16 + x) * 4;
        imageData[offset] = 0xFF;     // Blue
        imageData[offset + 1] = 0xFF; // Green
        imageData[offset + 2] = 0xFF; // Red
        imageData[offset + 3] = 0xFF; // Alpha
    }
});

// Create AND mask (all zeros for no transparency)
const andMask = Buffer.alloc(16 * 16 / 8); // 1 bit per pixel

// Combine all parts
const favicon = Buffer.concat([icoHeader, bmpHeader, imageData, andMask]);

// Write to file
fs.writeFileSync('public/favicon.ico', favicon);
console.log('Favicon created successfully!');
console.log('File size:', favicon.length, 'bytes');
