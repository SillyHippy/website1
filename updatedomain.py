import os
import re

def update_domain_references(directory):
    for root, _, files in os.walk(directory):
        for file in files:
            file_path = os.path.join(root, file)
            try:
                with open(file_path, 'r', encoding='utf-8') as f:
                    content = f.read()
                updated_content = content.replace("justlegalsolutions.org", "justlegalsolutions.org")
                if updated_content != content:
                    with open(file_path, 'w', encoding='utf-8') as f:
                        f.write(updated_content)
                    print(f"Updated domain references in: {file_path}")
            except Exception as e:
                print(f"Skipped {file_path} due to error: {e}")

if __name__ == "__main__":
    directory = input("Enter the directory path to scan: ")
    if os.path.isdir(directory):
        update_domain_references(directory)
        print("Domain reference updates completed.")
    else:
        print("Invalid directory path.")
