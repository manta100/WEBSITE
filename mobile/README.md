# Mobile App Build Guide

## IMPORTANT: android/ and ios/ Folders

The `android/` and `ios/` folders are **NOT pre-created**. They are generated when you run the setup script.

**Run this ONE TIME to create them:**
```bash
cd /workspaces/WEBSITE/mobile
chmod +x setup.sh
./setup.sh
```

## Prerequisites

### For Android:
- Node.js 18+
- Android SDK
- Java JDK 11+
- Android Studio (optional, for emulator)

### For iOS (Mac only):
- Node.js 18+
- Xcode 14+
- CocoaPods
- Apple Developer Account

## Quick Start (Full Setup)

### 1. Run the setup script (creates android/ios folders)
```bash
cd /workspaces/WEBSITE/mobile
chmod +x setup.sh
./setup.sh
```

### 2. Build Web App (after changes)
```bash
npm run build
npx cap sync
```

### 3. Open in IDE
```bash
npx cap open android   # Opens Android Studio
npx cap open ios      # Opens Xcode (Mac only)
```

## Building APK (Android)

### Option 1: Using the script
```bash
chmod +x build-android.sh
./build-android.sh
```

### Option 2: Manual
```bash
cd android
./gradlew assembleDebug
```

The APK will be at: `android/app/build/outputs/apk/debug/app-debug.apk`

## Building IPA (iOS)

### Option 1: Using Xcode
```bash
cd ios
open *.xcworkspace
```
Then in Xcode: Product > Build > Product > Archive > Distribute

### Option 2: Command Line
```bash
cd ios
xcodebuild -workspace *.xcworkspace -scheme App -configuration Release -archivePath build/App.xcarchive archive
xcodebuild -exportArchive -archivePath build/App.xcarchive -exportOptionsPlist ExportOptions.plist -exportPath output
```

## Capacitor Commands

```bash
# Open in Android Studio
npx cap open android

# Open in Xcode
npx cap open ios

# Copy web build to native
npx cap copy

# Sync web build to native
npx cap sync

# Update native dependencies
npx cap update
```

## Configuration

### Android App Signing

1. Create a keystore:
```bash
keytool -genkey -v -keystore my-release-key.keystore -alias my-key-alias -keyalg RSA -keysize 2048 -validity 10000
```

2. Configure in `android/app/build.gradle`:
```gradle
android {
    signingConfigs {
        release {
            storeFile file('my-release-key.keystore')
            storePassword 'password'
            keyAlias 'my-key-alias'
            keyPassword 'password'
        }
    }
}
```

### iOS App Signing

1. Open Xcode: `npx cap open ios`
2. Select your Team in Signing & Capabilities
3. Set Bundle Identifier: `com.saaspos.app`
4. Set Version and Build Number

## Troubleshooting

### Android Build Issues
- Clear Gradle cache: `cd android && ./gradlew clean`
- Update Android SDK
- Check JAVA_HOME is set correctly

### iOS Build Issues
- Run `pod install` in ios folder
- Update CocoaPods: `pod repo update`
- Check Xcode command line tools

## File Structure

```
mobile/
├── src/                 # React source code (you edit these)
│   ├── screens/         # React screen components
│   ├── components/      # Reusable components
│   ├── stores/          # State management (Context)
│   ├── services/        # API services
│   └── styles/          # CSS styles
├── public/              # Static assets
├── android/             # GENERATED - Android native project
├── ios/                 # GENERATED - iOS native project
├── capacitor.config.json # Capacitor configuration
├── setup.sh             # FIRST RUN - creates android/ios
└── build-android.sh     # Build APK script
```

**NOTE:** `android/` and `ios/` folders don't exist yet. Run `./setup.sh` to generate them.

## Features

- Touch-optimized POS interface
- Offline data caching
- Native barcode scanning (via camera)
- Haptic feedback
- Push notifications ready
- Dark mode support
