import os
from bs4 import BeautifulSoup

def replace_payment_text_in_html_tag_class(html_file_path):
    """
    Replaces specific text within <p class='elementor-icon-box-description'> tags in the HTML file.
    """
    try:
        with open(html_file_path, 'r', encoding='utf-8') as file:
            html_content = file.read()

        soup = BeautifulSoup(html_content, 'html.parser')

        # Target <p> tags with class "elementor-icon-box-description"
        paragraph_tags = soup.find_all('p', class_='elementor-icon-box-description')

        text_to_replace = """At this time, we accept electronic payments,, cash, checks, or money orders. Payments should be made payable to Joseph Iannazzi and mailed to: 564 E 138th Pl, Glenpool, OK 74033. Please ensure that payment is made in full within 10 days of the invoice date."""
        replacement_text = """At this time, we accept electronic payments, cash, checks, or money orders. Payments should be made payable to Joseph Iannazzi and mailed to: 564 E 138th Pl, Glenpool, OK 74033. Please ensure that payment is made in full within 10 days of the invoice date."""

        tags_modified_count = 0
        for p_tag in paragraph_tags:
            if p_tag.string and p_tag.string.strip() == text_to_replace.strip():
                p_tag.string.replace_with(replacement_text)
                tags_modified_count += 1

        if tags_modified_count > 0:
            with open(html_file_path, 'w', encoding='utf-8') as file:
                file.write(str(soup.prettify()))
            print(f"Successfully replaced payment text in {tags_modified_count} tag(s) in: {html_file_path}")
        else:
            print(f"No matching <p class='elementor-icon-box-description'> tags with text found in: {html_file_path} - skipping.")

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
                replace_payment_text_in_html_tag_class(html_file_path)

if __name__ == "__main__":
    repo_directory = input("Enter the path to your GitHub repository's root directory: ")
    if not os.path.isdir(repo_directory):
        print(f"Error: '{repo_directory}' is not a valid directory.")
    else:
        print(f"Processing index.html files for payment text replacement in <p class='elementor-icon-box-description'> tags in: {repo_directory}")
        process_directory(repo_directory)
        print("Finished processing index.html files.")