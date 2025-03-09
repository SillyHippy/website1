class ImageRatio {
    /**
     * This class is responsible for counting Matching Aspect Ratio.
     *
     * @param {aspectRatio} aspectRatio aspect ratios of editable image.
     */
    constructor( aspectRatio ) {
        this.allowableAspectRatios = [
            "Landscape (4:3)",
            "Landscape (16:9)",
            "Ultrawide (21:9)",
            "Landscape (3:2)",
            "Square (1:1)",
            "Portrait (2:3)",
            "Portrait (9:16)"
        ];
        this.allowedAspectRatiosFloat = [
            1.3333333333333333,
            1.7777777777777777,
            2.3333333333333335,
            1.5,
            1.0,
            0.6666666666666666,
            0.5625];
        ;
        this.aspectRatio = aspectRatio;
    }

    /**
     * This method processes an image to ensure it matches the allowable sizes and aspect ratios defined.
     * If the image's size is not in the allowable sizes, the method finds the closest
     * matching aspect ratio return.
     *
     * @returns {string} - aspect ratio in allowableAspectRatios format.
     */
    process() {
        let matchingAspectRatioFromAllowed = this.getMatchingAspectRatio();
        if (matchingAspectRatioFromAllowed) {
           return matchingAspectRatioFromAllowed;
        }
        return this.findProximateSize();
    }


    /**
     * Get the matching aspect ratio for the given image ratio.
     *
     * @param {number} tolerance - The tolerance level for comparing aspect ratios.
     * @returns {string}/{bool} - aspect ratio in allowableAspectRatios format.
     */
    getMatchingAspectRatio(tolerance = 1e-9) {
        for (let idx = 0; idx < this.allowedAspectRatiosFloat.length; idx++) {
            let allowedAspectRatio = this.allowedAspectRatiosFloat[idx];
            if (Math.abs(this.aspectRatio - allowedAspectRatio) < tolerance) {
                return this.allowableAspectRatios[idx];
            }
        }

        return false;
    }

    /**
     * Find the closest allowable size for the given image.
     *
     * @returns {string} - The corresponding allowable size.
     */
    findProximateSize() {
        let closestAspectRatio = 0;
        let closestIdx = 0;
        for (let idx = 0; idx < this.allowedAspectRatiosFloat.length; idx++) {
            let allowedAspectRatio = this.allowedAspectRatiosFloat[idx];
            if (Math.abs(this.aspectRatio - allowedAspectRatio) < Math.abs(this.aspectRatio - closestAspectRatio)) {
                closestAspectRatio = allowedAspectRatio;
                closestIdx = idx;
            }
        }
        return this.allowableAspectRatios[closestIdx];
    }
}
