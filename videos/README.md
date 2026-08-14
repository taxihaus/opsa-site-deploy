Drop the real Hero background video here as `hero.mp4`.

Until then, `src/sections/Hero.tsx` falls back to the exported Figma still frame
(`src/assets/images/hero-still.png`) as the `<video poster>` and as an `<img>`
underneath, so the section renders correctly either way.
