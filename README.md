# Clone
```
git clone https://github.com/maffey-com/freestuff_public.git
```

# PHP Composer
```
cd composer
composer install
```

# Docker

You need to have docker installed locally.

## Build
```
docker compose up -d --build 
```

## Run
```
docker compose up -d
```

# Database
You can connect to the local database with the following credentials
```
root:thing1@localhost:3306
```
To create an empty database and import some starting data, run the following commands
```
docker exec -i freestuff-mysql sh -c 'mysql -uroot -pthing1 < /tmp/freestuff.sql'
```
Create a temporary listing ids file
```
mkdir ./storage/site_files
echo 1 > ./storage/site_files/temporary_listing_ids.txt
```

# Usage
local frontend url: 
http://localhost:8087/

Backend url:
http://localhost:8087/cr

Email is captured by mailhog:
http://localhost:8025/

test user credentials:
```
email: admin@freestuff.co.nz
password: password
```



# Suggestions to implement

1. ~~Category Tags for when creating a new listing. ~~
2. Tabs for categories in the nav.
3. ~~Change dropdown list to a typing bar dropdown that narrows based on typed text.~~
4. move the upload picture button into the box.
5. restyle the upload picture box to look more modern.
6. move the freestuff app download down to the bottom above footer.
7. Have a Show all page replace browse listings.\
a. In the Show all page have a side bar filter setting and locations.\
b. In the Show all page have option to show number of listings.\
c. Have a tab to sort by date added.\
d. Center Page Select.
8. 

