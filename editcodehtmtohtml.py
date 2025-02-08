import os
import re

def update_htm_references(directory):
    for root, _, files in os.walk(directory):
        for file in files:
            file_path = os.path.join(root, file)
            try:
                with open(file_path, 'r', encoding='utf-8') as f:
                    content = f.read()
                
                updated_content = re.sub(r'(?<!\.html)\.html(?!l)', '.html', content)
                
                if updated_content != content:
                    with open(file_path, 'w', encoding='utf-8') as f:
                        f.write(updated_content)
                    print(f"Updated .html references in: {file_path}")
            except Exception as e:
                print(f"Skipped {file_path} due to error: {e}")

if __name__ == "__main__":
    directory = input("Enter the directory path to scan: ")
    if os.path.isdir(directory):
        update_htm_references(directory)
        print(".html to .html updates completed.")
    else:
        print("Invalid directory path.")
