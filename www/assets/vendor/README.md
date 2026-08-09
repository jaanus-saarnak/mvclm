# Third-party front-end code

Every JavaScript library and stylesheet the browser loads is served from this repository at a
pinned version, so none of them can change under the application without a commit here.

Each folder carries its own licence file. **None of this code is covered by the framework's own
`LICENSE`** at the repository root, which applies only to code written for MVCLM.

| folder | version | licence | source |
|---|---|---|---|
| `jquery/` | 1.11.1 | MIT | <https://jquery.com> |
| `chartjs/` | 4.5.1 | MIT | <https://www.chartjs.org> |
| `datatables/` | 1.13.7 | MIT | <https://datatables.net> |

**Everything here is MIT, and that is a rule rather than a coincidence.** Only MIT code is
bundled, so the repository carries one licence throughout. A dependency under different terms is
loaded at runtime instead, which is why the Inter webfont comes from Google Fonts and is not in
this folder.

## Where each is used

- **jQuery** loads on every page. Both layouts use it to fade out flash messages, so it is not
  only there for the two admin pages.
- **Chart.js** loads on the analytics page only.
- **DataTables** loads on the users page only.

