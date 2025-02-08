import os

def rename_index_files(directory):
    for root, _, files in os.walk(directory):
        for file in files:
            if file.lower() == "index.htm":
                old_path = os.path.join(root, file)
                new_path = os.path.join(root, "index.html")
                if not os.path.exists(new_path):  # Ensure it does not already exist
                    os.rename(old_path, new_path)
                    print(f"Renamed: {old_path} -> {new_path}")

if __name__ == "__main__":
    directory = input("Enter the directory path to scan: ")
    if os.path.isdir(directory):
        rename_index_files(directory)
        print("Renaming completed.")
    else:
        print("Invalid directory path.")
