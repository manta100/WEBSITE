#!/bin/bash

# SaaS POS Mobile Build Script

echo "=========================================="
echo "SaaS POS Mobile Build Script"
echo "=========================================="

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

cd /workspaces/WEBSITE/mobile

# Step 1: Install dependencies
echo -e "${YELLOW}Installing dependencies...${NC}"
npm install

# Step 2: Build web app
echo -e "${YELLOW}Building web app...${NC}"
npm run build

# Step 3: Initialize Capacitor
echo -e "${YELLOW}Initializing Capacitor...${NC}"
npx cap init SaaSPOS com.saaspos.app --web-dir=build

# Step 4: Add platforms
echo -e "${YELLOW}Adding Android platform...${NC}"
npx cap add android

echo -e "${YELLOW}Adding iOS platform...${NC}"
npx cap add ios

# Step 5: Sync to native platforms
echo -e "${YELLOW}Syncing to native platforms...${NC}"
npx cap sync

echo -e "${GREEN}Build preparation complete!${NC}"
echo ""
echo "To build APK:"
echo "  cd android && ./gradlew assembleDebug"
echo ""
echo "To build iOS (requires Mac):"
echo "  cd ios && open *.xcworkspace"
