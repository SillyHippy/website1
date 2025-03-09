class ImagesVersions {
    constructor() {
        this.images = {};
        this.undoStack = {};
        this.redoStack = {};
    }

    updateImages(newImages, imageName = null) {
        newImages = this.sortDescObj(newImages);
        const images = this.images[imageName] || {};
        if (Object.keys(images).length) {
            const newImagesByName = newImages[imageName] || {};
            const diff = this.diff(newImagesByName, images);

            this.undoStack[imageName] = {...(this.undoStack[imageName] || {}), ...diff};
            this.redoStack[imageName] = {};
        }
        this.images = newImages;
        this.undoStack = this.getUndoStack();
        this.redoStack = this.getRedoStack();

    }

    sortDescObj(obj) {
        let objLength = Object.keys(obj).length;
        if( !objLength ) return obj;
        let sortedObject = {};
        for( let i = (objLength-1); i >= 0; i--) {
            if( typeof obj['image_'+i] !== 'undefined' ) {
                sortedObject['image_'+i] = obj['image_'+i];
            }
        }
        return sortedObject;
    }

    undoImage(imageName = null) {
        const undoKeys = Object.keys(this.undoStack[imageName]);
        if (undoKeys.length > 1) {
            const itemVersion = undoKeys.pop();
            const redoImages = {};
            redoImages[itemVersion] = this.images[imageName][itemVersion];
            this.redoStack[imageName] = {...this.redoStack[imageName], ...redoImages};
            delete this.undoStack[imageName][itemVersion];
        }
    }

    redoImage(imageName = null) {
        const redoKeys = Object.keys(this.redoStack[imageName]);
        if (redoKeys.length > 0) {
            const itemVersion = redoKeys.pop();
            const undoImages = {};
            undoImages[itemVersion] = this.images[imageName][itemVersion];
            this.undoStack[imageName] = {...this.undoStack[imageName], ...undoImages};;
            delete this.redoStack[imageName][itemVersion];
        }
    }

    getImageCurrentVersion(imageName = null) {
        if (imageName) {
            if (this.undoStack[imageName]) {
                return Object.keys(this.undoStack[imageName]).pop();
            }
            return Object.keys(this.images[imageName]).pop();
        }
        return null;
    }

    getUndoStack(imageName = null) {
        const images = JSON.parse(JSON.stringify(this.images));
        if (imageName) {
            return this.undoStack[imageName] || images[imageName] || {};
        }

        return {...this.undoStack, ...this.diff(images, this.undoStack)};
    }

    getRedoStack(imageName = null) {
        const images = JSON.parse(JSON.stringify(this.images));
        if (imageName) {
            return this.redoStack[imageName] || {};
        }
        const diff = this.diff(images, this.redoStack, {});

        return {...this.redoStack, ...diff};
    }

    diff(obj1, obj2, defaultValue = null) {
        return Object.keys(obj1).reduce((diff, key) => {
            if (!obj2.hasOwnProperty(key)) {
                diff[key] = defaultValue || obj1[key];

            }
            return diff;
        }, {});
    }
}
