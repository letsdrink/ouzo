# Releasing Ouzo Goodies

Ouzo Goodies is published as a separate package (`letsdrink/ouzo-goodies`) extracted from the main Ouzo repository via `git subtree`.

## Steps

### 1. Prepare the release in the main repo

- Update `CHANGELOG.md` with a new version section describing breaking changes and enhancements.
- If the minimum PHP version changed, update `src/Ouzo/Goodies/README.md` accordingly.
- Commit and merge everything to `master`.

### 2. Make sure the `ouzo-goodies` remote is configured

```bash
git remote add ouzo-goodies git@github.com:letsdrink/ouzo-goodies.git
```

Skip if the remote already exists (`git remote -v` to check).

### 3. Split the Goodies subtree

```bash
git subtree split --prefix=src/Ouzo/Goodies -b ouzo-goodies
```

If the branch already exists from a previous release, delete it first:

```bash
git branch -D ouzo-goodies
git subtree split --prefix=src/Ouzo/Goodies -b ouzo-goodies
```

### 4. Push to the ouzo-goodies repository

```bash
git push ouzo-goodies ouzo-goodies:master
```

### 5. Tag the release

```bash
git tag X.Y.Z ouzo-goodies
git push ouzo-goodies X.Y.Z
```

Replace `X.Y.Z` with the actual version number (e.g. `4.0.0`).

### 6. Create a GitHub release

Go to [github.com/letsdrink/ouzo-goodies/releases](https://github.com/letsdrink/ouzo-goodies/releases), create a new release for the tag, and paste the release notes from `CHANGELOG.md`.
