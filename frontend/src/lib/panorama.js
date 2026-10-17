export function buildPanoramaConfig(storageBaseUrl, panoramaPath) {
  const normalizedBase = String(storageBaseUrl || 'http://localhost:8000/storage').replace(/\/$/, '');
  const path = String(panoramaPath || '');
  const full = `${normalizedBase}/${path}`;

  // Demo multiresolution panoramas follow: panoramas/scene-XX/preview.jpg
  const marker = '/preview.jpg';
  if (path.includes(marker)) {
    const tileDir = path.slice(0, path.lastIndexOf(marker));
    return {
      type: 'multires',
      multiRes: {
        basePath: `${normalizedBase}/${tileDir}/tiles`,
        path: '/%l/%s%y_%x',
        fallbackPath: '/fallback/%s',
        extension: 'jpg',
        tileResolution: 256,
        maxLevel: 2,
        cubeResolution: 1024,
      },
    };
  }
  return { type: 'equirectangular', panorama: full };
}
