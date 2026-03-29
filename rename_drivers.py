import os

year = '2025'
directory = 'drivers_2024'

for filename in os.listdir(directory):
    if filename.endswith(".png"):
        # Split the name and the extension
        name, ext = os.path.splitext(filename)
        
        # Construct the new name
        # new_name = f"{name}{ext}" if (year in name) else f"{name}_{year}{ext}"
        new_name = f"{name[:-5]}{ext}"
        
        # Perform the rename
        os.rename(os.path.join(directory, filename), 
                    os.path.join(directory, new_name))

print("Renaming complete!")