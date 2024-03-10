# This module controls the depth gizmo.
from browser import alert, document, window, html, svg


# Global variables
gizmoYPos = -1
isDepthGizmoDown = False

y1RectDepth = -1
y2RectDepth = -1


def onGizmoDepthDown(event):
    global isDepthGizmoDown, gizmoYPos

    gizmoYPos = event.y
    isDepthGizmoDown = True

    # While the button is pressed, the whole document will be listening to events. This is so events work
    # outside the normally active elements.
    svgroot = document['root']
    svgroot.style['pointer-events'] = 'all'

def onGizmoDepthUp(event):
    global isDepthGizmoDown, gizmoYPos

    if isDepthGizmoDown:
        isDepthGizmoDown = False
        gizmoYPos = -1

        # Makes the document unresponsive again (except for the normally avtive elements in the gizmo).
        # This allows leaflet to handle the rest of events.
        svgroot = document['root']
        svgroot.style['pointer-events'] = 'none'

        # Consolidates the transforms.
        transformList = document['gizmoDepthHandle'].transform.baseVal
        transformList.consolidate()

def onGizmoDepthMove(event):
    global isDepthGizmoDown, gizmoYPos, pos

    if isDepthGizmoDown:

        dy = event.y - gizmoYPos
        gizmoYPos = event.y

        # Moves the handle with the mouse pointer.
        svgroot = document['root']
        translate = svgroot.createSVGTransform()
        mat = svgroot.getScreenCTM() # This is to convert screen coordinates into SVG units.
        translate.setTranslate(0, dy/mat.d) # Hack: mat.b is the y scale of the document

        # Check that the Depth handle is between the limits
        rect = document['gizmoDepthHandle'].getBoundingClientRect()
        yHandle = rect.top + rect.height/2.0
        dy = yHandle - y1RectDepth
        if dy < 0:
            translate.setTranslate(0, -dy/mat.d)
        dy = y2RectDepth - yHandle
        if dy < 0:
            translate.setTranslate(0, dy/mat.d)

        # Consolidates the transforms.
        transformList = document['gizmoDepthHandle'].transform.baseVal
        transformList.appendItem(translate)
        transformList.consolidate()

        # Compute the location of the handle (as a value in [0,1]) and the date corresponding to this
        # location
        pos = (yHandle - y1RectDepth)/(y2RectDepth - y1RectDepth)
        pos = max(min(pos, 1.0), 0.0)
        depth = depth1 + pos * (depth2 - depth1)
        gizmoDepthText = document['gizmoDepthText2']
        pos = round(5*pos)
        if pos == 0:
            gizmoDepthText.text = 'Surface'
        else:
            gizmoDepthText.text = '%i m' % pos

def setupDepthGizmo(dep1, dep2, visible = True):
    global y1RectDepth, y2RectDepth
    global depth1, depth2

    depth1 = dep1
    depth2 = dep2

    if visible:
        document["gizmoDepthHandle"].bind("mousedown", onGizmoDepthDown)
        document["gizmoDepthHandle"].bind("mouseup",   onGizmoDepthUp)
        document["gizmoDepthHandle"].bind("mousemove", onGizmoDepthMove)
        document["root"            ].bind("mousemove", onGizmoDepthMove)
        document["root"            ].bind("mouseup",   onGizmoDepthUp)

        rect = document['rectDepthGizmo'].getBoundingClientRect()
        y1RectDepth = rect.top
        y2RectDepth = rect.bottom
        # print (rect.__dict__)

        document["DepthControl"]["visibility"] = "visible"

    else: # if visible is False
        document["DepthControl"]["visibility"] = "hidden"
