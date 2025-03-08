import os
from bs4 import BeautifulSoup, NavigableString

def add_payment_link_to_html_tag_class(html_file_path):
    """
    Adds a hyperlink to 'electronic payments' within <p class='elementor-icon-box-description'> tags.
    """
    try:
        with open(html_file_path, 'r', encoding='utf-8') as file:
            html_content = file.read()

        soup = BeautifulSoup(html_content, 'html.parser')

        # Target <p> tags with class "elementor-icon-box-description"
        paragraph_tags = soup.find_all('p', class_='elementor-icon-box-description')

        link_added_count = 0
        for p_tag in paragraph_tags:
            if p_tag.string and "At this time, we accept electronic payments" in p_tag.string:
                text_content = p_tag.string
                link_text = "electronic payments"
                replacement_link = soup.new_tag("a", href="https://buy.stripe.com/3cs17SbHS6h95nGaEE", target="_blank") # Added target="_blank"
                replacement_link.string = link_text

                # Split the text content around "electronic payments"
                parts = text_content.split(link_text, 1) # Split only once

                # Clear existing contents of the <p> tag
                p_tag.clear()

                # Append the parts and the link tag back to the <p> tag
                p_tag.append(NavigableString(parts[0])) # Add text before the link
                p_tag.append(replacement_link)        # Add the link
                p_tag.append(NavigableString(parts[1])) # Add text after the link

                link_added_count += 1


        if link_added_count > 0:
            with open(html_file_path, 'w', encoding='utf-8') as file:
                file.write(str(soup.prettify()))
            print(f"Successfully added hyperlink to 'electronic payments' in {link_added_count} tag(s) in: {html_file_path}")
        else:
            print(f"No matching <p class='elementor-icon-box-description'> tags with payment text found in: {html_file_path} - skipping.")


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
                add_payment_link_to_html_tag_class(html_file_path)

if __name__ == "__main__":
    repo_directory = input("Enter the path to your GitHub repository's root directory: ")
    if not os.path.isdir(repo_directory):
        print(f"Error: '{repo_directory}' is not a valid directory.")
    else:
        print(f"Processing index.html files to add hyperlink to 'electronic payments' in <p class='elementor-icon-box-description'> tags in: {repo_directory}")
        process_directory(repo_directory)
        print("Finished processing index.html files.")