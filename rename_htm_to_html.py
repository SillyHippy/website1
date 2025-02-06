import os

# Set the directory where you want to rename files (change to your repo folder path if needed)
directory = "."

# Walk through all directories and files
for root, dirs, files in os.walk(directory):
    for filename in files:
        if filename.endswith(".htm"):
            # Generate new file name by replacing .htm with .html
            new_filename = filename.replace(".htm", ".html")
            old_path = os.path.join(root, filename)
            new_path = os.path.join(root, new_filename)

            # Rename the file
            os.rename(old_path, new_path)
            print(f"Renamed: {old_path} → {new_path}")

print("All .htm files have been renamed to .html")
