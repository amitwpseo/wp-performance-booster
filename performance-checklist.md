# WordPress Performance Audit Checklist

Use this comprehensive checklist to audit and optimize any WordPress site for speed and Core Web Vitals.

## Hosting & Server
- [ ] Choose a managed WordPress host or optimized VPS
- [ ] Ensure PHP version is 8.0+ (latest stable)
- [ ] Verify server has sufficient memory (at least 256MB)
- [ ] Enable OPcache for PHP
- [ ] Use a CDN (Cloudflare, KeyCDN, etc.)
- [ ] Ensure server-level caching is enabled

## WordPress Core Settings
- [ ] Update to latest WordPress version
- [ ] Remove unused themes (keep only active + one default)
- [ ] Delete inactive plugins
- [ ] Set permalinks to "Post name" for optimal structure
- [ ] Disable "Trackbacks" and "Pingbacks"
- [ ] Limit post revisions (define `WP_POST_REVISIONS` in wp-config.php)

## Caching
- [ ] Install and configure a caching plugin (WP Rocket, W3 Total Cache, etc.)
- [ ] Enable page caching
- [ ] Enable browser caching
- [ ] Enable object caching (Redis/Memcached if available)
- [ ] Minify HTML, CSS, and JavaScript
- [ ] Combine CSS and JS files (test carefully)

## Image Optimization
- [ ] Compress all images (lossless compression)
- [ ] Convert images to modern formats (WebP, AVIF)
- [ ] Implement lazy loading for images
- [ ] Set explicit width and height attributes
- [ ] Serve images in appropriate sizes (responsive images)
- [ ] Use a CDN for image delivery
- [ ] Implement image sitemap for SEO

## Database Optimization
- [ ] Clean up post revisions
- [ ] Remove spam and trashed comments
- [ ] Delete expired transients
- [ ] Optimize database tables
- [ ] Schedule regular database maintenance
- [ ] Remove orphaned postmeta and termmeta

## CSS Optimization
- [ ] Minify CSS files
- [ ] Remove unused CSS (use tools like PurgeCSS)
- [ ] Inline critical CSS above the fold
- [ ] Defer non-critical CSS
- [ ] Reduce CSS specificity where possible
- [ ] Avoid @import (use <link> instead)

## JavaScript Optimization
- [ ] Minify JavaScript files
- [ ] Defer parsing of JavaScript
- [ ] Add async/defer attributes to non-critical JS
- [ ] Move scripts to footer where possible
- [ ] Remove jQuery if not needed (or use a recent version)
- [ ] Combine JavaScript files (test carefully)

## Fonts Optimization
- [ ] Limit font variations (weights, styles)
- [ ] Use system fonts when possible
- [ ] Preload critical fonts
- [ ] Use font-display: swap
- [ ] Self-host fonts (avoid Google Fonts external requests)
- [ ] Subset fonts for specific languages

## Plugins & Theme
- [ ] Audit plugins – remove any not absolutely necessary
- [ ] Use a lightweight, performance-optimized theme
- [ ] Avoid page builders with heavy frontend assets
- [ ] Check for plugin conflicts affecting performance
- [ ] Update all plugins to latest versions
- [ ] Test theme performance with and without plugins

## Core Web Vitals Optimization

### Largest Contentful Paint (LCP)
- [ ] Optimize server response time (<200ms)
- [ ] Eliminate render-blocking resources
- [ ] Optimize images (compress, modern formats, responsive)
- [ ] Preload LCP image
- [ ] Minimize main-thread work
- [ ] Minimize JavaScript execution time

### First Input Delay (FID)
- [ ] Reduce JavaScript execution time
- [ ] Break up long tasks
- [ ] Optimize third-party code
- [ ] Use a web worker when possible
- [ ] Minimize main thread blocking

### Cumulative Layout Shift (CLS)
- [ ] Set explicit width/height for images and videos
- [ ] Reserve space for ads and embeds
- [ ] Avoid inserting content above existing content
- [ ] Use transform animations instead of layout-changing properties
- [ ] Preload fonts to avoid FOIT/FOUT

## Monitoring & Maintenance
- [ ] Set up performance monitoring (GTmetrix, PageSpeed Insights)
- [ ] Create performance budget (max page size, request count)
- [ ] Schedule weekly performance checks
- [ ] Monitor Core Web Vitals in Google Search Console
- [ ] Keep changelog of performance improvements

## Tools to Use
- Google PageSpeed Insights
- GTmetrix
- WebPageTest
- Chrome DevTools (Lighthouse)
- Pingdom Tools
- Query Monitor (plugin)
- WP Rocket or similar caching plugin
- Smush or ShortPixel for images

## Quick Wins (First Things to Do)
1. Enable caching plugin
2. Compress all images
3. Minify CSS/JS
4. Remove unused plugins
5. Update PHP version
6. Enable GZIP compression
7. Implement lazy loading
8. Set up CDN

---

**Pro Tip**: Run a performance test before and after each change to measure impact. Keep a log of what improves scores and what doesn't.
