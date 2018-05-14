import { PhotoArtsPage } from './app.po';

describe('photo-arts App', () => {
  let page: PhotoArtsPage;

  beforeEach(() => {
    page = new PhotoArtsPage();
  });

  it('should display welcome message', () => {
    page.navigateTo();
    expect(page.getParagraphText()).toEqual('Welcome to app!!');
  });
});
