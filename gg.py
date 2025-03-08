import os
from bs4 import BeautifulSoup

def add_global_css_link_to_html(html_file_path):
    """Adds the global.css link tag to the <head> of an HTML file if it's not already present."""
    try:
        with open(html_file_path, 'r', encoding='utf-8') as file:
            html_content = file.read()

        soup = BeautifulSoup(html_content, 'html.parser')
        head_tag = soup.find('head')

        if head_tag:
            link_tag = soup.new_tag('link', rel='stylesheet', href='global.css')
            # Check if the link tag already exists (to avoid duplicates)
            existing_link_tag = head_tag.find('link', rel='stylesheet', href='global.css')
            if not existing_link_tag:
                # Insert the link tag as the first child of the <head>
                head_tag.insert(0, link_tag)

                with open(html_file_path, 'w', encoding='utf-8') as file:
                    file.write(str(soup.prettify()))  # prettify() for formatted HTML
                print(f"Successfully added global.css link to: {html_file_path}")
            else:
                print(f"global.css link already exists in: {html_file_path} - skipping.")
        else:
            print(f"Warning: <head> tag not found in {html_file_path}. Skipping.")

    except FileNotFoundError:
        print(f"Error: File not found: {html_file_path}")
    except Exception as e:
        print(f"Error processing {html_file_path}: {e}")

def process_directory(repo_path):
    """Walks through the repository directory and processes index.html files."""
    for root, _, files in os.walk(repo_path):
        for file in files:
            if file == 'index.html':
                html_file_path = os.path.join(root, file)
                add_global_css_link_to_html(html_file_path)

if __name__ == "__main__":
    repo_directory = input("Enter the path to your GitHub repository's root directory: ")
    if not os.path.isdir(repo_directory):
        print(f"Error: '{repo_directory}' is not a valid directory.")
    else:
        print(f"Processing index.html files in repository: {repo_directory}")
        process_directory(repo_directory)
        print("Finished processing index.html files.")