# old-www
This website is what I created in my high school computer science classes during 2012-2014. I thought it would be fun to resurrect the code and do a little write-up on the security issues that I didn't know about when I started programming.

## Docker
I Dockerized the original codebase to make it easier for anyone to jump in. To start the site, just clone and repo and use Docker Compose:
```bash
git clone https://github.com/gharryg/old-www.git
cd old-www
docker compose up
```
Outside of Docker and the changes below, the website and the code are exactly the way they were around 2014. I tried my best to preserve the accuracy of the codebase.

## Changes
* Added two default users
  * Admin user: admin@gharryg.com
  * Normal user: user@gharryg.com
  * Both use the same password: `asdfasdf`
* Changed hard-coded links and file paths
* Removed phpMyAdmin and dev site links
* Removed "About" page and "Minecraft Server" page
  * The Minecraft page used [mcstatus](https://github.com/attrib/mcstatus) to display bits of information from my Minecraft server - which was running on an HP Compaq dc7700 at my parent's house
* Disabled outbound email
  * Account verification and password reset links are now provided directly on the page
* Removed Google Analytics and Google site verification
* Replaced self-hosted video with YouTube equivilant
* Removed broken return logic for sign-in/sign-out scripts
* Reduced PHP logging
* Removed broken error page files and references
* Removed non-existant entries from robots.txt
* Removed IE check/warnings
* Fixed Same Game block loading bug in loadImages()
