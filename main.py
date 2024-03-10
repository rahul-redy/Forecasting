from browser import alert, document, window, html, svg, ajax, timer
import javascript
import struct
import datetime

import html as HTML

from dateGizmo  import *
from depthGizmo import *
from colorBar   import *
from conf       import *
from layersMenu import *
from maps       import *


isPeeking = False

request = None

curDate = None




# dateStart = datetime.datetime(2019, 2, 16, 8, 0)
# dateEnd   = datetime.datetime(2019, 2, 22, 8, 0)

dateStart = datetime.datetime(2023, 1,  7, 0, 0, 0, 0, datetime.timezone.utc)
dateEnd   = datetime.datetime(2025, 10, 2, 0, 0, 0, 0, datetime.timezone.utc)

nowDate = javascript.Date.new(javascript.Date.now())
dateFile  = datetime.datetime(nowDate.getYear()+1900, nowDate.getMonth()+1, nowDate.getUTCDate(), 0, 0, 0, 0, datetime.timezone.utc)

document.getElementById('fileDate').valueAsDate = nowDate

# Access the leaflet.js API
leaflet = window.L

crs = leaflet.CRS.EPSG4326




def onDateChange(layer, date):
    # the date time changes (detetime *inside* a file, do not confuse with onFileDateChanged)
    global curDate

    mapLayers.onDateChange(date)


def onFileDateChange(event):
    # the date of the data file changes (do not confuse with onFileChange)
    dateFile = datetime.datetime.strptime(event.target.value, '%Y-%m-%d')
    mapLayers.clearAll()

    mapLayers.AddAll(dateFile)

    curDate = event.target.value
    setupDateGizmo(mapLayers.mainLayer, None, None, mapLayers.dates[:], onDateChange, conf)
    setupDepthGizmo(0, 10, False)

    pass



def onPointerMove(event):
    global isPeeking, layerName, mapLayers

    map = mapLayers.map
    latlngPointer = map.mouseEventToLatLng(event)

    xy2 = map.mouseEventToContainerPoint(event)
    x = xy2.x
    y = xy2.y

    if isPeeking:
        mapLayers.peekValues(latlngPointer.lat, latlngPointer.lng)

        document['textCoords2'].text = '%.3f, %.3f' % (latlngPointer.lat, latlngPointer.lng)
        document['rectCoords'].attributeStyleMap.set('opacity', 1)


def onPointerDown(event):
    global isPeeking

    # Makes the SVG not to listen to mouse events (leaflet will still listening)
    if isPeeking:
        svgroot = document['root']
        svgroot.style['pointer-events'] = 'none'


    isPeeking = False
    hideColorBarsValueBox(mapLayers.colorBars)

    # Hides the rectangle with the label
    document['rectCoords'].attributeStyleMap.set('opacity', 0)
    document['textCoords2'].text = ''


def onBtnPointClick(event):
    global isPeeking, map

    isPeeking = True
    showColorBarsValueBox(mapLayers.colorBars)

    # Allows for events and changes the cursor to cross hair
    document['root'].style.cursor = 'crosshair'
    document['root'].style.pointerEvents = 'all'

##########################################################################edited by Rahul Reddy #################################s

##########################################################################edited by Rahul Reddy #################################
# conf = Conf('confSNB.xml')

# main.py

# main.py
# Log the loaded configuration content


# main.py

# main.py

from browser import document, alert

# Function to load configuration based on code
def load_config_by_code(loaded_code):
    loaded_config_path = f'users_data/config_{loaded_code}.xml'

    # Check if the file exists
    if conf_exists(loaded_config_path):
        # Set the configuration path and file name
        conf_path = f'users_data/config_{loaded_code}.xml'

        # Log the loaded configuration information using print
        print('Loaded Configuration Path:', conf_path)

        # Return the configuration path and file name
        return conf_path
    else:
        # Handle the case where the file doesn't exist
        alert(f'Configuration file not found for code: {loaded_code}')
        return None
    
# Function to check if a configuration file exists
def conf_exists(conf_path):
    try:
        # Try to open the file
        open(conf_path)
        return True
    except IOError:
        # File not found
        return False

# Check if a loaded code parameter is present in the URL
loaded_code = window.location.search.split('=')[1] if 'loadedCode' in window.location.search else None

# Log the loaded code using print
print('Loaded Code:', loaded_code)

if loaded_code:
    # The loaded code is present in the URL, use it to load the configuration
    conf_path = load_config_by_code(loaded_code)

    # Check if the conf_path is not None
    if conf_path:
        # Set the configuration using the returned path
        conf = Conf(conf_path)
    else:
        # Handle the case where the configuration loading failed
        conf = None
else:
    # The loaded code parameter is not present, use the default configuration
    print('Error loading config content')
    conf = Conf('confHurricanes.xml')










mapLayers = Maps(dateFile, crs, conf, leaflet)


# Creates the menu with the available layers
# setupLayersMenu(conf, mapLayers)
setupLayersMenu2(conf, mapLayers)


parser = window.DOMParser.new()


try:
    server = conf.servers[0]
    fileCapabilities = open(server['capabilitiesreq'].format(wmsURL = server['url'], layerName = conf.layers[0]['name'], strTime = '2019-02-04T15:00:00.000Z'))
    capabilities = fileCapabilities.read()


    tree = parser.parseFromString(capabilities, "application/xml")
    capabilities = None
    # b = document(a)
    root = tree.firstChild.firstChild

    elemDimension = tree.getElementsByTagName('Dimension')

except:
    pass


# Put marker on map
# leaflet.marker([xyz.latitude, xyz.longitude]).addTo(map)

document["root"].bind("mousemove", onPointerMove)
document["root"].bind("mousedown", onPointerDown)

document["btnPoint"].bind("mouseup", onBtnPointClick)
document["btnPoint"].bind("onclick", onBtnPointClick)

document["fileDate"].bind("change", onFileDateChange)



curDate = dateStart
try:
    setupDateGizmo(mapLayers.mainLayer, None, None, mapLayers.dates[:], onDateChange, conf)
    setupDepthGizmo(0, 10, False)
except:
    pass

# cmap = setupCMap(document, [0,0.5,1], ['#f0ff1a', '#ffffff', '#3370d7'], -50, 50)


# Hides the rectangle with the pointer values label
document['rectCoords'].attributeStyleMap.set('opacity', 0)
document['textCoords2'].text = ''

