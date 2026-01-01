export interface marketingModel {
  name?: string;
  platform?: string;
  sms_template?: string,
  email_template?: string,
  sort_order?: string,
  status?: string;
  created_by?: string,
  updated_by?: string,
  createdBy?: string;
  createAt?: string;
  updatedAt?: string;
}



export interface ListResult {
  code: number;
  data: {
    items: Array<marketingModel>,
    currentPage: number,
    lastPage: number,
    pageSize: number,
    total: number,
    totalPage: number,
  };
  message: string;
}

export interface searchParameter {
  name?: string;
  platform?: string;
  status?: string,
  page?: number;
  pageSize?: number;
  sortBy?: string;
  descending?: boolean;
}


