import os

def add_favicon_to_html(html_file_path):
    """
    Replaces existing (incorrect) favicon links with new favicon links using GitHub blob URLs in an HTML file.

    Args:
        html_file_path (str): The path to the HTML file to modify.
    """

    incorrect_favicon_code_block = """
<link rel="icon" type="image/png" href="/https://github.com/SillyHippy/website1/blob/main/oPKyDQlOdjnFtEbtOfVCRpiYRmCLmZut/favicon.svg/favicon-96x96.png" sizes="96x96" />
<link rel="icon" type="image/svg+xml" href="/https://github.com/SillyHippy/website1/blob/main/oPKyDQlOdjnFtEbtOfVCRpiYRmCLmZut/favicon.svg/favicon.svg" />
<link rel="shortcut icon" href="/https://github.com/SillyHippy/website1/blob/main/oPKyDQlOdjnFtEbtOfVCRpiYRmCLmZut/favicon.svg/favicon.ico" />
<link rel="apple-touch-icon" sizes="180x180" href="/https://github.com/SillyHippy/website1/blob/main/oPKyDQlOdjnFtEbtOfVCRpiYRmCLmZut/favicon.svg/apple-touch-icon.png" />
<meta name="apple-mobile-web-app-title" content="JLS" />
<link rel="manifest" href="/https://github.com/SillyHippy/website1/blob/main/oPKyDQlOdjnFtEbtOfVCRpiYRmCLmZut/favicon.svg/site.webmanifest" />
    """

    github_blob_url_base = "https://github.com/SillyHippy/website1/blob/main/oPKyDQlOdjnFtEbtOfVCRpiYRmCLmZut"
    correct_favicon_code_block = f"""
    <link rel="icon" type="image/png" href="{github_blob_url_base}/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{github_blob_url_base}/favicon.svg" />
    <link rel="shortcut icon" href="{github_blob_url_base}/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="{github_blob_url_base}/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="JLS" />
    <link rel="manifest" href="{github_blob_url_base}/site.webmanifest" />
    """

    try:
        with open(html_file_path, 'r') as f:
            html_content = f.read()

        # Remove the incorrect favicon code block if it exists
        updated_html_content = html_content.replace(incorrect_favicon_code_block, "")

        head_tag_index = updated_html_content.lower().find('<head>')

        if head_tag_index != -1:
            # Insert the correct favicon code block after the <head> tag
            head_end_tag_pos = updated_html_content.lower().find('>', head_tag_index) + 1
            updated_html_content = updated_html_content[:head_end_tag_pos] + correct_favicon_code_block + updated_html_content[head_end_tag_pos:]


            with open(html_file_path, 'w') as f:
                f.write(updated_html_content)
            print(f"Incorrect favicon code replaced with correct code in '{html_file_path}'")
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
        print("Favicon replacement process completed for all HTML files.")
    else:
        print("Directory not found.")