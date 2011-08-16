The current state of the box.net PHP library is terrible. I can't even get it to work. 
It throws errors right out of the box. That isn't the way a "library" should 
behave. 

Tired of trying to coerce it into working (I never succeeded), I just re-wrote it. 
And the, of course, I continued to make little improvements here and there until.. I
now have the current iteration of Box_Rest_Client.

There are a lot of things to fix, and a lot of things that I haven't accounted for 
but my use-cases tend to be rather small.. I was hoping that by pushing the class 
publicly we could hunt down bugs and edge cases faster, resulting in a more robust 
(also working) version of the API.

If you have any issues, PLEASE log them on the GitHub issues page.

For information on how the client works, take a look at the example.php file which 
will be updated as more features are added.