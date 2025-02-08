import os

# Set the target directory
directory = r"C:\Users\ianna\OneDrive\Desktop\GitHub\website1"

# Check if the directory exists
if not os.path.exists(directory):
    print(f"Error: Directory '{directory}' does not exist.")
    exit()

# Process files and folders recursively
for root, _, files in os.walk(directory):
    for filename in files:
        old_file_path = os.path.join(root, filename)

        # Rename .htm files to .html
        if filename.endswith(".htm"):
            new_file_path = os.path.join(root, filename[:-4] + ".html")
            os.rename(old_file_path, new_file_path)
            print(f"Renamed: {old_file_path} -> {new_file_path}")
            old_file_path = new_file_path  # Update file path after renaming

        # Update references to index.html inside all files
        try:
            with open(old_file_path, "r", encoding="utf-8") as file:
                content = file.read()

            updated_content = content.replace("index.html", "index.htmll")

            if content != updated_content:  # Only write if changes were made
                with open(old_file_path, "w", encoding="utf-8") as file:
                    file.write(updated_content)
                print(f"Updated references in: {old_file_path}")

        except Exception as e:
            print(f"Error processing {old_file_path}: {e}")
