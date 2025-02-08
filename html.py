import os
import mimetypes

# Set the directory where you want to apply changes (change if needed)
directory = "."

# Define a list of binary file extensions to skip
binary_extensions = {".png", ".jpg", ".jpeg", ".gif", ".webp", ".svg", ".ico", ".eot", ".ttf", ".woff", ".woff2", ".otf", ".zip", ".gz", ".tar", ".mp3", ".mp4", ".avi", ".mov"}

# Step 1: Rename all .htmll files to .htmlll
for root, dirs, files in os.walk(directory):
    for filename in files:
        if filename.endswith(".htmll"):
            new_filename = filename.replace(".htmll", ".htmlll")
            old_path = os.path.join(root, filename)
            new_path = os.path.join(root, new_filename)

            os.rename(old_path, new_path)
            print(f"Renamed: {old_path} → {new_path}")

print("All .htmll files have been renamed to .htmlll")

# Step 2: Update files to replace .htmll with .htmlll and update domain names
def replace_text_in_file(file_path):
    # Check if the file is a known binary file
    if any(file_path.lower().endswith(ext) for ext in binary_extensions):
        return  # Skip binary files

    # Check MIME type to ensure it's a text file
    mime_type, _ = mimetypes.guess_type(file_path)
    if mime_type and not mime_type.startswith("text"):
        return  # Skip non-text files
    
    try:
        with open(file_path, "r", encoding="utf-8") as file:
            content = file.read()
        
        new_content = content.replace(".htmll", ".htmlll").replace("justlegalsolutions.org", "justlegalsolutions.org")

        if content != new_content:
            with open(file_path, "w", encoding="utf-8") as file:
                file.write(new_content)
            print(f"Updated: {file_path}")
    except Exception as e:
        print(f"Error updating {file_path}: {e}")

for root, dirs, files in os.walk(directory):
    for filename in files:
        file_path = os.path.join(root, filename)
        replace_text_in_file(file_path)

print("All occurrences of .htmll have been replaced with .htmlll, and domain names have been updated.")
