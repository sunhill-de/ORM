# InfoMarket

## What is InfoMarket?
InfoMarket is a information exchange system for unified access to informations regardless of their origin. Information can be read or written to.

## How does it work?
Imagine you want to buy something. Of course you can go to a supermarket and put everything in your cart but you want to go to a bazaar. But in in this bazar you are not allowed to enter the market hall so you have to go to a salesperson and ask for the desired item. The salesperson goes in the market hall and asks the single marketeers "Do you have the item 'xy'?" When one marketter says yes the salesperson collects it and brings it to you. You can also give him a shopping list and he collects all items at once and brings them to you. If this market hasn't got the item, it's possible that the salesperson calls another market and asks for the item. In our case the item is a piece of information and the salesperson is the InfoMarket interface. 

## Core concepts
1. The InfoMarket should provide as few methods as possible, all kinds of informations should be accessed through them 
2. The result of any method should be a json-object (or StdClass in php). One field must be "state" which can be "OK" or "FAILED".
3. All informations are accessed through one ore more hirarchic strings ("system.hardware.hdd.capacity") 
4. Astreriks are allowed in the information identifier too ("system.hardware.`*`") or ("home.*.light")
5. Value, unit and semantic meaning are seperated entities (like "3", "seconds", "Duration")
6. Units are normalized (A °C is always a °C)
7. Semantic meaning should be normalized as far as possible and they should be hirarchic (meaning "running time" is a sub class of "duration") 

## Information flow
* All requests are performed through the InfoMarket facade or (with less overhead) directly to the InfoMarket class. 
* All marketeers that are registered to the InfoMarket, are asked if the have the desired information.
* If yes, the information is added to the answer. 
* Depending if you only need one anwer or all answers the search is stopped or continued with the remaining marketeers
* After all marketeers are asked the collected answers (or the only one or no answer) are given back. 