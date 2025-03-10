import os

file_path = r"C:\Users\ianna\OneDrive\Desktop\GitHub\website1\wp-content\plugins\elementor\assets\js\editor.min.js"  # Raw string for Windows path

def modify_javascript_file(filepath):
    """
    Modifies the editor.min.js file to disable favicon processing.

    This script will:
    1. Comment out the 'if(T.headFavicon...' line.
    2. Change 'headFavicon: !0' to 'headFavicon: !1' in the Oe object.

    WARNING: This modifies the file directly. Back up your file before running.
    """

    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            lines = f.readlines()

        modified_lines = []
        favicon_if_line_found = False
        oe_headfavicon_line_found = False

        for line in lines:
            # 1. Comment out the if(T.headFavicon... line
            if not favicon_if_line_found and line.strip().startswith('if(T.headFavicon&&("link"===C.tagName'):
                modified_lines.append(f'// {line}')  # Comment out the line
                favicon_if_line_found = True
            # 2. Change headFavicon: !0 to headFavicon: !1 in Oe object
            elif not oe_headfavicon_line_found and 'headFavicon:!0' in line:
                modified_lines.append(line.replace('headFavicon:!0', 'headFavicon:!1'))
                oe_headfavicon_line_found = True
            else:
                modified_lines.append(line)

        if not favicon_if_line_found:
            print("Warning: 'if(T.headFavicon...' line not found. It might already be modified or absent.")
        if not oe_headfavicon_line_found:
            print("Warning: 'headFavicon:!0' line in Oe object not found. It might already be modified or absent.")

        with open(filepath, 'w', encoding='utf-8') as f:
            f.writelines(modified_lines)

        print(f"Successfully modified '{filepath}'. Favicon processing should be disabled.")

    except FileNotFoundError:
        print(f"Error: File not found at '{filepath}'. Please check the file path.")
    except Exception as e:
        print(f"An error occurred: {e}")
        print("File modification may have failed. Please check the file manually.")

if __name__ == "__main__":
    backup_filepath = file_path + ".backup"
    try:
        import shutil
        shutil.copy2(file_path, backup_filepath)
        print(f"Backup created at: '{backup_filepath}'")
    except Exception as backup_err:
        print(f"Warning: Backup creation failed: {backup_err}. Please manually back up '{file_path}' before proceeding.")

    modify_javascript_file(file_path)