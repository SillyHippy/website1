import os

# Set the directory where you want to search (change to your repo folder path if needed)
directory = "."

# Function to replace .htm with .html in the file content
def replace_extension_in_file(file_path):
    with open(file_path, "r", encoding="utf-8") as file:
        content = file.read()

    # Replace all occurrences of .htm with .html
    new_content = content.replace(".htm", ".html")

    # Write the new content back to the file
    with open(file_path, "w", encoding="utf-8") as file:
        file.write(new_content)

    print(f"Updated: {file_path}")

# Walk through all directories and files
for root, dirs, files in os.walk(directory):
    for filename in files:
        if filename == "index.html":
            file_path = os.path.join(root, filename)
            replace_extension_in_file(file_path)

print("All occurrences of .htm have been replaced with .html in index.html files.")
