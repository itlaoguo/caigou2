export interface ImportMetaEnv {
  readonly VITE_IS_REQUEST_PROXY: string;
  readonly VITE_API_URL: string;
  readonly VITE_API_URL_PREFIX: string;
  readonly VITE_DOWNLOAD_TEMPLATE_URL?: string;
  // 在这里添加更多 .env.site 中定义的变量
}
