# flibusta-downloader
Downloading files from Flibusta.is

# Currently:
1. List of URLs should be in *flibusta.txt*
2. URL should start with *https://flibusta.is/b/ + numbers*
3. *.env* should contain:

```
SAVE_FOLDER = 'path/to/folder'
COOKIE_KEY = 'SESS717db4750c98b34dc0a0cf14a0c49e88'
COOKIE_VAL = 'SESS717db4750c98b34dc0a0cf14a0c49e88 value from browser settings after logging in'
```

# Options
## Optional
**--useRemoteFilename**: Remain filename as server return. Otherwise (default) file will be renamed to format *Author — Title— Year*
