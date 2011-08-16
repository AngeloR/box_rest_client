## What is it?
The Box_Rest_Client is a simple way to access the Box.net api utilizing the ReST 
api through PHP. _The client is currently in early alpha testing and is 
undergoing daily fixes and features._ If you have any questions please email me 
at xangelo@gmail.com. For all bugs/issues, please report them on GitHub.

## Dependencies
- cURL
- SimpleXML
- PHP version 5.3x (This is where it has been tested.. it continues the pass 
tests in other vresions, please let me know and this can be updated)

## How does it work?
The `Box_Rest_Client` provides a standard way to execute various api methods from 
the box.net api (version 1.0 at the time of writing this). While the library aims 
to be very low-level (I'm not making any assumptions on what you want to do with 
the data) that inherently makes it quite powerful. 

Rather than providing "aliases" to the box.net api, the client completely relies 
on the api_methods. Except in a few cases (authentication, folder_tree, etc.) you 
will mostly be calling the api methods directly. 

This happens through the use of the exec() method which acceps an API method as 
well as any parameters that need to be passed. As an example, to use the 
get_account_tree api method you would do something like the following (note, the 
example assumes you have already set an api_key and authenticated a user).

```$box_rest_client = new Box_Rest_Client($api_key);

$box_rest_client->exec('get_account_tree',array('params[]'=>'nozip'));```

Of course there are "aliases" for commonly used api methods to ensure that you 
don't need to do a lot of work to get up and running with the box.net api. 

For more details and some code examples, take a look at the example.php file. 

## Aliases
Aliases are simply methods with the Box_Rest_Client class that can be run to 
perform a series of actions instead of forcing you to run through a series of 
api-methods manually. 

### Authentication 
_replaces[[get_ticket](http://developers.box.net/w/page/12923936/ApiFunction_get_ticket),[get_auth_token](http://developers.box.net/w/page/12923930/ApiFunction_get_auth_token)]_
To authenticate a user simply set the api_key and then do the following
```$box_rest_client->authenticate(); ```

The authentication will kick in and get a token, which it will use to redirect 
the user to the box.net login screen where they will need to grant your 
application permission. Once that is complete, they will be redirected back to 
your callback page. From there, you can do what you want. 

What happens with the auth_token that is received from the completion of the 
authentication process is handled via the `Box_Rest_Client_Auth->store()` 
method. By default it simply returns the `auth_token`, but you could 
theoretically set it up to do whatever you wanted to make the auth_token 
publicly available. 

After you have the auth-token, you will need to provide it to the 
`Box_Rest_Client` class every time.


### File/Folder list
_replaces[[get_account_tree](http://developers.box.net/w/page/12923929/ApiFunction_get_account_tree)]_
Since the `get_account_tree` api method returns a result structure that varies 
based on the setup of a certain directory, the get_account_tree api method is 
aliased as ```$box_rest_client->folder(0)```

That will instantly return a list of files/folders below the folder wth id 0. 
It will also ensure that the folders/files are of type `Box_Client_Folder`/`Box_Client_File` 
respectively. These provide a standard interface for accessing properties of 
the files and will eventually mean that we can add inidividual features to it.

## Bugs/Issues 
If you find any issues while utilizing the Box_Rest_Client, please log them on 
GitHub instead of emailing me. 