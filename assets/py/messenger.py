import os, os.path, simplejson, time, hashlib, urllib2

#
#Setting up dictionaries to search through
#

#Get all of the lists out there
lists = {}
for root, _, files in os.walk("/var/www/remembot/assets/db/lists"):
  for f in files:
    fullpath = os.path.join(root, f)
    with open(fullpath, "r") as json:
      json = simplejson.load(json)
      lists[json["id"]] = json

#Get all of there users out there
users = {}
for root, _, files in os.walk("/var/www/remembot/assets/db/users"):
  for f in files:
    fullpath = os.path.join(root, f)
    with open(fullpath, "r") as json:
      json = simplejson.load(json)
      users[json["id"]] = {"ifttt_key":json["ifttt_key"],"salt":json["salt"]}

#Get all of there recipients out there
recipients = {}
for root, _, files in os.walk("/var/www/remembot/assets/db/recipients"):
  for f in files:
    fullpath = os.path.join(root, f)
    with open(fullpath, "r") as json:
      json = simplejson.load(json)
      recipients[json["id"]] = {
        "list": json["list"],
        "contact": json["contact"],
        "active": json["active"]
      }

#Get all the messages out there
messages = {}
for root, _, files in os.walk("/var/www/remembot/assets/db/messages"):
  for f in files:
    fullpath = os.path.join(root, f)
    with open(fullpath, "r") as json:
      json = simplejson.load(json)
      #Skip sent messages or those that are in the future still
      if json["sent"] == 1 or json["date"] > int(time.time())+30:
        continue

      messages[json["id"]] = {
        "message": json["message"],
        "list": json["list"],
        "file": fullpath
      }

#
#Actually sending out the messages
#

#Loop through and send messages
for mKey, msg in messages.iteritems():
  #Get the list that the message is in
  thisList = lists[json["list"]]

  #Get the owner of the list the message is in
  thisUser = users[thisList["owner"]]

  for rKey, r in recipients.iteritems():
    #Skip recipients not for this message
    if r["list"] != msg["list"] or r["active"] == 0:
      continue

    #Payload to send
    data = {"value1": r["contact"], "value2": msg["message"], "value3": ""}

    key = str(thisUser["ifttt_key"])
    eventName = hashlib.sha512()
    eventName.update(thisList["name"] + thisUser["salt"])
    eventName = "RememBot_" + eventName.hexdigest()[0:5].upper()

    url = "https://maker.ifttt.com/trigger/" + eventName + "/with/key/" + key

    req = urllib2.Request(url)
    req.add_header('Content-Type', 'application/json')
    response = urllib2.urlopen(req, simplejson.dumps(data))

  with open(messages[mKey]["file"], "r") as message:
    body = simplejson.load(message)
    body["sent"] = 1
    body = simplejson.dumps(body, sort_keys=True, indent=4 * " ")
    with open(messages[mKey]["file"], "w") as msgF:
      msgF.write(body)