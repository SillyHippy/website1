import os
import re

def replace_in_github_repo(repo_path, old_domain, new_domain):
    """
    Searches for and replaces a domain name in files within a GitHub repository,
    excluding Python files.

    Args:
        repo_path (str): The path to the root directory of the GitHub repository.
        old_domain (str): The domain name to search for and replace.
        new_domain (str): The domain name to replace with.
    """

    for root, _, files in os.walk(repo_path):
        for file in files:
            if file.endswith(".py"):  # Skip Python files
                continue

            file_path = os.path.join(root, file)

            try:
                with open(file_path, "r", encoding="utf-8") as f:
                    file_content = f.read()

                # Use regex to find the domain and replace
                new_content = re.sub(re.escape(old_domain), new_domain, file_content)


                if new_content != file_content:
                    with open(file_path, "w", encoding="utf-8") as f:
                        f.write(new_content)
                    print(f"Replaced '{old_domain}' with '{new_domain}' in: {file_path}")

            except Exception as e:
                print(f"Error processing {file_path}: {e}")

if __name__ == "__main__":
    repo_path = "."  # Replace with the actual path to your repo
    old_domain = "justlegalsolutions.tech"
    new_domain = "justlegalsolutions.org"

    replace_in_github_repo(repo_path, old_domain, new_domain)
    print("Done!")