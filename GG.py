import os

def add_favicon_to_html(html_file_path):
    """
    Adds favicon links and meta tags to the <head> section of an HTML file.

    Args:
        html_file_path (str): The path to the HTML file to modify.
    """

    favicon_code = """
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="JLS" />
    <link rel="manifest" href="/site.webmanifest" />
    """

    try:
        with open(html_file_path, 'r') as f:
            html_content_lines = f.readlines()

        head_tag_index = -1
        for i, line in enumerate(html_content_lines):
            if '<head>' in line.lower():
                head_tag_index = i
                break

        if head_tag_index != -1:
            # Insert favicon code after the <head> tag
            html_content_lines.insert(head_tag_index + 1, favicon_code)

            with open(html_file_path, 'w') as f:
                f.writelines(html_content_lines)
            print(f"Favicon code successfully added to '{html_file_path}' in '{html_file_path}'")
        else:
            print(f"Error: <head> tag not found in '{html_file_path}'.")

    except FileNotFoundError:
        print(f"Error: File not found at '{html_file_path}'.")
    except Exception as e:
        print(f"An error occurred: {e}")

def process_directory(directory_path):
    """
    Processes all HTML files within a directory and its subfolders.

    Args:
        directory_path (str): The path to the directory to process.
    """
    if not os.path.isdir(directory_path):
        print(f"Error: '{directory_path}' is not a valid directory.")
        return

    for root, _, files in os.walk(directory_path):
        for file in files:
            if file.lower().endswith(('.html', '.htm')):
                html_file_path = os.path.join(root, file)
                add_favicon_to_html(html_file_path)

if __name__ == "__main__":
    dir_path = input("Enter the path to your website directory: ")
    if os.path.exists(dir_path) and os.path.isdir(dir_path):
        process_directory(dir_path)
        print("Favicon addition process completed for all HTML files.")
    else:
        print("Directory not found.")