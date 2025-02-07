import os

# Set the directory where you want to apply changes (change if needed)
directory = "."

# Step 1: Rename all .htm files to .html
for root, dirs, files in os.walk(directory):
    for filename in files:
        if filename.endswith(".htm"):
            new_filename = filename.replace(".htm", ".html")
            old_path = os.path.join(root, filename)
            new_path = os.path.join(root, new_filename)

            os.rename(old_path, new_path)
            print(f"Renamed: {old_path} → {new_path}")

print("All .htm files have been renamed to .html")

# Step 2: Update index.html files to replace .htm with .html in content
def replace_extension_in_file(file_path):
    with open(file_path, "r", encoding="utf-8") as file:
        content = file.read()

    new_content = content.replace(".htm", ".html")

    with open(file_path, "w", encoding="utf-8") as file:
        file.write(new_content)

    print(f"Updated: {file_path}")

for root, dirs, files in os.walk(directory):
    for filename in files:
        if filename == "index.html":
            file_path = os.path.join(root, filename)
            replace_extension_in_file(file_path)

print("All occurrences of .htm have been replaced with .html in index.html files.")
