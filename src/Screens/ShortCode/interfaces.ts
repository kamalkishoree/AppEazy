export interface initBootInterface {
  auth: any,
  themeToggle: boolean,
  themeColor: boolean,
  deepLinkUrl: string
  languages: {
    primary_language: {}
  },
  currencies: {
    primary_currency: {}
  },
}
export interface IRootState {
  initBoot: initBootInterface;
  auth: userDataInterface;
}

export interface userDataInterface {
  auth_token: string;
  userData: object;
}
