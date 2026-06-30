--- # skill: fix-issue

description: review the github issue solve the issue and submit a PR fix. Reach for this skill whenever I ask you to fix the GitHub issue. 

---

# fixing github issues

when working github issues in this project, follow this approach.

## getting context

Always fetch the issue details first to understand the full context:

``bash
gh issue view <issue_number> --json title body comments
``

Read title, body, dicription and labels to identify:
- affected files and components
- type of fix needed
- any related context

## branching

create a branch using naming convention `fix/issue-<issue_number>`.

```
git checkout -b fix/issue-<issue_number>
```

## implementation standards
- follow existing code conventions
- write test that match the style of existing tests
- ensure existing tests still pass
- run `composer run test:coverage`  before considering the work complete and make it 100% coverage compulsoary and all green
- run `composer run analyze` to check for any static analysis issues no error should be present

## commiting

use this commit message format:

```
Fix: <issue_title> (#<issue_number>)
```

## creating PR

push and create a PR using the `gh` cli:

```bash
gh pr create --title "Fix: <issue_title> (#<issue_number>)" --body "$(cat << 'EOF'

## Summary

brief summary what was fixed

## Changes

- list of changes made

Closes #<issue_number>
EOF
)"
```

the PR should
- refrence closes #<issue_number> in the body
- summarize what was fixed
- flag any concerns or areas needing review

## output

when complete, provide me with brief summary:
- issue number and title
- what was changed
- link to the PR
