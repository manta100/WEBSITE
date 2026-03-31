#!/bin/bash

# SaaS POS Mobile - Complete Setup Script
# Run this script ONCE to generate android/ios folders

set -e

echo "=========================================="
echo "SaaS POS Mobile Setup"
echo "=========================================="

cd /workspaces/WEBSITE/mobile

# Check Node.js
if ! command -v node &> /dev/null; then
    echo "Error: Node.js is not installed"
    exit 1
fi

# Step 1: Install dependencies
echo -e "\n[1/6] Installing npm dependencies..."
npm install

# Step 2: Build web app
echo -e "\n[2/6] Building web app..."
npm run build

# Step 3: Create placeholder icon
echo -e "\n[3/6] Creating placeholder icon..."
mkdir -p public/assets/icons

# Create a simple SVG icon and convert to PNG using base64
# This creates a blue square icon
cat > public/assets/icons/icon.svg << 'EOF'
<svg xmlns="http://www.w3.org/2000/svg" width="512" height="512" viewBox="0 0 512 512">
  <rect width="512" height="512" rx="64" fill="#3b82f6"/>
  <text x="256" y="320" font-family="Arial" font-size="280" font-weight="bold" fill="white" text-anchor="middle">POS</text>
</svg>
EOF

echo "Created icon.svg - Replace with your app icon"

# Step 4: Initialize Capacitor
echo -e "\n[4/6] Initializing Capacitor..."
npx cap init SaaSPOS com.saaspos.app --web-dir=build --skip-confirmation 2>/dev/null || true

# Step 5: Add Android
echo -e "\n[5/6] Adding Android platform..."
npx cap add android

# Step 6: Add iOS
echo -e "\n[6/6] Adding iOS platform..."
npx cap add ios

# Sync to native
echo -e "\n[Final] Syncing to native platforms..."
npx cap sync

echo ""
echo "=========================================="
echo "Setup Complete!"
echo "=========================================="
echo ""
echo "Folders created:"
echo "  - android/  (Android Studio project)"
echo "  - ios/      (Xcode project)"
echo ""
echo "To build APK:"
echo "  cd android && ./gradlew assembleDebug"
echo ""
echo "To open in Xcode (Mac only):"
echo "  cd ios && open *.xcworkspace"
echo ""
echo "APK will be at:"
echo "  android/app/build/outputs/apk/debug/app-debug.apk"
