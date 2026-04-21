interface JQuery {
    /**
     * Initialize ripples effect with options
     */
    ripples(options?: {
        resolution?: number;
        dropRadius?: number;
        perturbance?: number;
        interactive?: boolean;
        crossOrigin?: string;
        imageUrl?: string | null;
    }): JQuery;

    /**
     * Call ripples methods
     */
    ripples(method: 'destroy' | 'show' | 'hide' | 'pause' | 'play' | 'updateSize'): JQuery;
    
    /**
     * Add a drop programmatically
     */
    ripples(method: 'drop', x: number, y: number, radius: number, strength: number): JQuery;
    
    /**
     * Update a property
     */
    ripples(method: 'set', name: 'dropRadius' | 'perturbance' | 'interactive' | 'imageUrl' | 'crossOrigin', value: any): JQuery;
}
