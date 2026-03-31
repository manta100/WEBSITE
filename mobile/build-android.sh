#!/bin/bash

# SaaS POS Android Build Script

echo "=========================================="
echo "Building Android APK"
echo "=========================================="

cd /workspaces/WEBSITE/mobile

# Build web app
npm run build

# Copy to Android
npx cap copy android
npx cap sync android

# Build APK
cd android

if [ -f "./gradlew" ]; then
    chmod +x ./gradlew
    ./gradlew assembleDebug
    
    if [ -f "app/build/outputs/apk/debug/app-debug.apk" ]; then
        echo ""
        echo -e "\033[0;32m=========================================="
        echo "APK Built Successfully!"
        echo "=========================================="
        echo "APK Location:"
        echo "  $(pwd)/app/build/outputs/apk/debug/app-debug.apk"
        echo ""
        cp app/build/outputs/apk/debug/app-debug.apk /workspaces/WEBSITE/mobile/saas-pos.apk
        echo "Copy saved to: /workspaces/WEBSITE/mobile/saas-pos.apk"
    fi
else
    echo "Error: gradlew not found. Run build.sh first."
fi
