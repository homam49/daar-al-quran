# GitHub Setup Instructions

To upload your Daar Al Quran project to GitHub, follow these steps:

1. Create a new repository on GitHub:
   - Go to https://github.com/new
   - Name your repository (e.g., "daar-al-quran")
   - Choose public or private visibility
   - Do NOT initialize with README, .gitignore, or license
   - Click "Create repository"

2. Connect your local repository to GitHub:
   ```
   git remote add origin https://github.com/YOUR-USERNAME/YOUR-REPOSITORY.git
   git branch -M main
   git push -u origin main
   ```

3. Enter your GitHub credentials when prompted.

4. Your code is now on GitHub!

## Deploying to Production

After uploading to GitHub, follow the steps in DEPLOYMENT.md to properly deploy the application to your production server.
