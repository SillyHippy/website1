import os

def rename_index_files(directory):
    for root, _, files in os.walk(directory):
        for file in files:
            if file.lower() == "index.html":
                old_path = os.path.join(root, file)
                new_path = os.path.join(root, "index.htmll")
                os.rename(old_path, new_path)
                print(f"Renamed: {old_path} -> {new_path}")

def update_references(directory):
    for root, _, files in os.walk(directory):
        for file in files:
            file_path = os.path.join(root, file)
            try:
                with open(file_path, 'r', encoding='utf-8') as f:
                    content = f.read()
                updated_content = content.replace('.html', '.htmll')
                if updated_content != content:
                    with open(file_path, 'w', encoding='utf-8') as f:
                        f.write(updated_content)
                    print(f"Updated references in: {file_path}")
            except Exception as e:
                print(f"Skipped {file_path} due to error: {e}")

if __name__ == "__main__":
    directory = input("Enter the directory path to scan: ")
    if os.path.isdir(directory):
        rename_index_files(directory)
        update_references(directory)
        print("Renaming and updating references completed.")
    else:
        print("Invalid directory path.")
