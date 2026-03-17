# WordPress Performance Booster

A collection of lightweight PHP snippets, optimization techniques, and best practices to supercharge your WordPress site speed, improve Core Web Vitals, and boost overall performance.

## Why Performance Matters
- Better user experience and engagement
- Higher search engine rankings (Google Core Web Vitals)
- Increased conversion rates
- Lower bounce rates

## What's Inside

### PHP Optimization Snippets
| File | Description |
|------|-------------|
| [`critical-css-generator.php`](critical-css-generator.php) | Generate and inline critical above-the-fold CSS for faster rendering |
| [`lazy-load-optimization.php`](lazy-load-optimization.php) | Advanced lazy loading for images, iframes, and videos |
| [`database-cleanup.php`](database-cleanup.php) | Scheduled cleanup of post revisions, spam, transients, and expired options |
| [`cdn-switcher.php`](cdn-switcher.php) | Easy CDN integration for all media files with URL rewriting |
| [`preload-critical-assets.php`](preload-critical-assets.php) | Preload fonts, key APIs, and above-the-fold images |
| [`remove-query-strings.php`](remove-query-strings.php) | Remove version query strings from CSS/JS for better caching |
| [`disable-emojis-embeds.php`](disable-emojis-embeds.php) | Disable unnecessary WordPress core features that add bloat |
| [`async-defer-js.php`](async-defer-js.php) | Add async/defer attributes to specific JavaScript files |
| [`heartbeat-control.php`](heartbeat-control.php) | Control WordPress Heartbeat API to reduce server load |
| [`gzip-compression.php`](gzip-compression.php) | Enable GZIP compression via PHP (fallback for .htaccess) |

### Best Practices Guides
| File | Description |
|------|-------------|
| [`performance-checklist.md`](performance-checklist.md) | Comprehensive step-by-step performance audit checklist |
| [`core-web-vitals-guide.md`](core-web-vitals-guide.md) | How to optimize for Largest Contentful Paint (LCP), First Input Delay (FID), and Cumulative Layout Shift (CLS) |
| [`hosting-recommendations.md`](hosting-recommendations.md) | Recommended WordPress hosting providers for different budgets |
| [`caching-strategies.md`](caching-strategies.md) | Page caching, browser caching, and object caching explained |
| [`image-optimization.md`](image-optimization.md) | Best practices for image formats, compression, and delivery |

### Sample .htaccess Rules
| File | Description |
|------|-------------|
| [`htaccess-optimized.txt`](htaccess-optimized.txt) | Optimized .htaccess with expires headers, compression, and caching rules |

## How to Use

### Option 1: Copy Individual Snippets
1. Browse the files above
2. Copy the code you need
3. Paste into your theme's `functions.php` or a custom plugin

### Option 2: Create a Performance Plugin
1. Download all files
2. Create a single plugin file that includes all snippets
3. Activate on client sites

> ⚠️ **Always test on a staging site first!**

## Requirements
- WordPress 5.0+
- PHP 7.4+

## Testing Tools
After implementing these optimizations, test your site with:
- [Google PageSpeed Insights](https://pagespeed.web.dev/)
- [GTmetrix](https://gtmetrix.com/)
- [WebPageTest](https://www.webpagetest.org/)
- [Chrome DevTools](https://developer.chrome.com/docs/devtools/)

## Contributing
Found a bug? Have an improvement? Feel free to open an issue or submit a pull request.

## License
MIT License – use freely in personal and commercial projects.

## About the Author
[Amit Nandi](https://github.com/amitwpseo) – Top Rated WordPress Developer and Local SEO Specialist. I use these exact snippets in client projects to deliver lightning-fast websites that rank higher and convert better.

## ⭐ If you find this useful, please star the repository!
