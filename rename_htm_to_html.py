import os

directory = "."  # Current directory

for filename in os.listdir(directory):
    if filename.endswith(".htm"):
        new_filename = filename.replace(".htm", ".html")
        os.rename(filename, new_filename)
        print(f"Renamed: {filename} → {new_filename}")

print("All .htm files have been renamed to .html")
